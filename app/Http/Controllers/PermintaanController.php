<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\DetailPermintaan;
use App\Models\Barang;
use App\Models\MutasiBarang;
use App\Models\DetailMutasiBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusPermintaan;
use Illuminate\Validation\ValidationException;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Permintaan/index', [
            'permintaans' => Permintaan::with('pemohon')
                ->latest('tanggal_pengajuan') // Urutkan dari yang terbaru
                ->paginate(10), // Pagination 10 item
            'filters' => request()->all('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Pengajuan/index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Pemohon
        $validated = $request->validate([
            'no_permintaan' => 'required|string|unique:permintaans,no_permintaan',
            'jenis_keperluan' => 'required|in:Jurusan,Program_Studi,Laboratorium,Bengkel,Bagian,Sub_Bagian,Unit,Urusan',
            'file_surat' => 'required|file|mimes:pdf,doc,docx|max:2048', // Validasi File
        ]);

        // 2. Upload File
        $path = null;
        if ($request->hasFile('file_surat')) {
            // Simpan di folder 'public/surat_permintaan'
            $path = $request->file('file_surat')->store('surat_permintaan', 'public');
        }

        // 3. Simpan ke Database (Header Saja)
        Permintaan::create([
            'no_permintaan' => $validated['no_permintaan'],
            'jenis_keperluan' => $validated['jenis_keperluan'],
            'pemohon_user_id' => Auth::id(), // Otomatis dari user login
            'status' => 'Pending', // Default awal
            'tanggal_pengajuan' => now(),
            'file_path' => $path,
            'is_transcribed' => false, // Belum ditranskripsi operator
        ]);

        return redirect()->route('permintaan.create')
            ->with('success', 'Permintaan berhasil diajukan. Menunggu proses operator.');
    }

    // ... show, edit, update, destroy (jika diperlukan) ...

    /**
     * Proses permintaan (oleh operator) - Halaman Transkripsi
     */        
    public function proses($id)
    {
        $permintaan = Permintaan::with([
            'pemohon',
            'detailPermintaans.barang.satuan'
        ])->findOrFail($id);

        // Tambahkan URL file untuk preview di frontend
        $permintaan->file_url = $permintaan->file_path
            ? asset('storage/' . $permintaan->file_path)
            : null;

        // Ambil data barang untuk dropdown, sertakan stok untuk validasi frontend
        $barangs = Barang::with(['satuan'])
            ->select('id','nama_barang','kode_barang','satuan_id','stok_saat_ini')
            ->orderBy('nama_barang')
            ->get();

        return Inertia::render('Permintaan/proses', [
            'permintaan' => $permintaan,
            'barangs' => $barangs
        ]);
    }

    /**
     * Simpan proses permintaan (oleh operator)
     */
    public function prosesStore(Request $request, $id)
    {
        // Validasi input dari form transkripsi
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Ambil header permintaan
            $permintaan = Permintaan::findOrFail($id);

            // 1️⃣ Hapus detail lama (untuk menghindari duplikasi jika diedit ulang)
            $permintaan->detailPermintaans()->delete();

            // 2️⃣ Simpan detail baru hasil transkripsi operator
            foreach ($validated['items'] as $item) {
                // Optional: Validasi stok di backend saat transkripsi (Peringatan Dini)
                // Meskipun eksekusi pengurangan stok ada di approval, 
                // mencegah input yang tidak mungkin dipenuhi sejak awal adalah praktik yang baik.
                $barang = Barang::findOrFail($item['barang_id']);
                if ($barang->stok_saat_ini < $item['jumlah']) {
                     throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi (Tersedia: {$barang->stok_saat_ini}, Diminta: {$item['jumlah']}). Silakan kurangi jumlah atau batalkan item ini.");
                }

                $permintaan->detailPermintaans()->create([
                    'barang_id' => $item['barang_id'],
                    'jumlah_diminta' => $item['jumlah'],
                    'keterangan' => $item['catatan'] ?? null,
                ]);
            }

            // 3️⃣ Update status menjadi Processed (Menunggu Approval)
            $permintaan->update([
                'status' => 'Processed',
                'operator_user_id' => Auth::id(), // Catat siapa operator yang memproses
                'is_transcribed' => true
            ]);

            DB::commit();

            return redirect()
                ->route('permintaan.index')
                ->with('success', 'Permintaan berhasil ditranskripsi dan diteruskan ke Approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve permintaan (oleh approval)
     */
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            // Load permintaan beserta detail barangnya untuk update stok
            $permintaan = Permintaan::with('detailPermintaans.barang')->findOrFail($id);

            // Validasi Status: Hanya Pending atau Processed yang bisa diapprove
            // (Idealnya hanya Processed yang sudah ditranskripsi operator)
            if (!in_array(
                $permintaan->status->value, // Asumsi StatusPermintaan adalah Enum
                [StatusPermintaan::PENDING->value, StatusPermintaan::PROCESSED->value]
            )) {
                return back()->with('error', 'Permintaan tidak dapat disetujui karena statusnya saat ini adalah ' . $permintaan->status->value . '.');
            }

            // ----------------------------------
            // 1. VALIDASI STOK SEBELUM EKSEKUSI
            // ----------------------------------
            foreach ($permintaan->detailPermintaans as $detail) {
                // Refresh data barang untuk mendapatkan stok terkini (karena bisa berubah sejak transkripsi)
                $barang = $detail->barang()->lockForUpdate()->first(); // Lock row agar tidak ada race condition

                if ($barang->stok_saat_ini < $detail->jumlah_diminta) {
                    throw new \Exception("Gagal Approve: Stok barang {$barang->nama_barang} saat ini tidak mencukupi! (Tersedia: {$barang->stok_saat_ini}, Diminta: {$detail->jumlah_diminta})");
                }
            }

            // ---------------------------
            // 2. UBAH STATUS PERMINTAAN
            // ---------------------------
            $permintaan->update([
                'status' => StatusPermintaan::APPROVED->value,
                'approval_user_id' => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);

            // ----------------------------------
            // 3. BUAT HEADER MUTASI BARANG (KELUAR)
            // ----------------------------------
            $mutasi = MutasiBarang::create([
                'jenis_mutasi' => 'keluar',
                'tanggal_mutasi' => now(),
                'no_dokumen' => $permintaan->no_permintaan, // Referensi ke No Surat Permintaan
                'no_bukti' => 'BUKTI-KELUAR-REQ-' . $permintaan->id, // Generate No Bukti Internal
                'keterangan' => 'Pengeluaran Barang untuk Permintaan No. ' . $permintaan->no_permintaan . ' (' . $permintaan->jenis_keperluan->value . ')',
                'dicatat_oleh_user_id' => auth()->id(),
                // 'permintaan_id' => $permintaan->id, // Kolom ini opsional jika Anda menambahkannya di migrasi mutasi_barangs
            ]);

            // Generate nomor mutasi otomatis jika belum di-handle model
            if (empty($mutasi->nomor_mutasi)) {
                 $mutasi->update([
                    'nomor_mutasi' => 'MB-OUT-' . date('Ymd') . '-' . str_pad($mutasi->id, 4, '0', STR_PAD_LEFT)
                ]);
            }

            // ----------------------------------
            // 4. EKSEKUSI PENGURANGAN STOK & DETAIL MUTASI
            // ----------------------------------
            foreach ($permintaan->detailPermintaans as $detail) {
                $barang = $detail->barang; // Object barang sudah diload di awal

                // Simpan detail mutasi
                DetailMutasiBarang::create([
                    'mutasi_barang_id' => $mutasi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $detail->jumlah_diminta,
                    'catatan' => $detail->keterangan,
                ]);

                // Update stok barang di Master
                $barang->decrement('stok_saat_ini', $detail->jumlah_diminta);
            }

            DB::commit();

            return back()->with('success', 'Permintaan disetujui. Stok barang telah dikurangi dan Mutasi Keluar tercatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject permintaan (oleh approval)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_reject' => 'required|string|max:255',
        ]);

        $permintaan = Permintaan::findOrFail($id);

        if (!in_array($permintaan->status->value, [StatusPermintaan::PENDING->value, StatusPermintaan::PROCESSED->value])) {
            return back()->with('error', 'Permintaan tidak dapat ditolak karena statusnya saat ini adalah ' . $permintaan->status->value . '.');
        }

        $permintaan->update([
            'status' => StatusPermintaan::REJECTED->value,
            'catatan_reject' => $request->catatan_reject,
            'approval_user_id' => auth()->id(),
            'tanggal_disetujui' => now(), // Tanggal keputusan (reject/approve)
        ]);

        return back()->with('success', 'Permintaan berhasil ditolak.');
    }

    /**
     * Halaman Riwayat Permintaan Pemohon
     */
    public function riwayat()
    {
        $userId = Auth::id();

        $permintaans = Permintaan::where('pemohon_user_id', $userId)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return Inertia::render('Permintaan/riwayat', [
            'permintaans' => $permintaans
        ]);
    }

    /**
     * Detail permintaan untuk pemohon
     */
    public function detail($id)
    {
        $permintaan = Permintaan::with([
            'pemohon',
            'detailPermintaans.barang.satuan'
        ])->where('id', $id)
        // ->where('pemohon_user_id', Auth::id()) // keamanan: hanya milik sendiri
        ->firstOrFail();

        $permintaan->file_url = $permintaan->file_path
            ? asset('storage/' . $permintaan->file_path)
            : null;

        return Inertia::render('Permintaan/detail', [
            'permintaan' => $permintaan
        ]);
    }
}