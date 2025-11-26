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

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permintaan = Permintaan::with(['pemohon'])->get();

        return Inertia::render('Permintaan/index', [
            'permintaans' => $permintaan,
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

    /**
     * Display the specified resource.
     */
    public function show(Permintaan $permintaan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permintaan $permintaan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permintaan $permintaan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permintaan $permintaan)
    {
        //
    }

    /**
     * Proses permintaan (oleh operator)
     */        
    public function proses($id)
    {
        $permintaan = Permintaan::with([
            'pemohon',
            'detailPermintaans.barang.satuan'
        ])->findOrFail($id);

        $permintaan->file_url = $permintaan->file_path
            ? asset('storage/' . $permintaan->file_path)
            : null;

        $barangs = Barang::with(['satuan'])
        ->select('id','nama_barang','satuan_id','stok_saat_ini')
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

            // 1️⃣ Hapus detail lama
            $permintaan->detailPermintaans()->delete();

            // 2️⃣ Simpan detail baru
            foreach ($validated['items'] as $item) {
                $permintaan->detailPermintaans()->create([
                    'barang_id' => $item['barang_id'],
                    'jumlah_diminta' => $item['jumlah'],
                    'keterangan' => $item['catatan'] ?? null,
                ]);
            }

            // 3️⃣ Update status
            $permintaan->update([
                'status' => 'Processed',
            ]);

            DB::commit();

            return redirect()
                ->route('permintaan.index')
                ->with('success', 'Permintaan berhasil diproses.');

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
            $permintaan = Permintaan::with('detailPermintaans')->findOrFail($id);

            if (!in_array(
                $permintaan->status->value,
                [StatusPermintaan::PENDING->value, StatusPermintaan::PROCESSED->value]
            )) {
                return back()->with('error', 'Permintaan tidak dapat disetujui karena statusnya saat ini adalah ' . $permintaan->status->value . '.');
            }

            // ---------------------------
            // 1. UBAH STATUS PERMINTAAN
            // ---------------------------
            $permintaan->update([
                'status' => StatusPermintaan::APPROVED->value,
                'approval_user_id' => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);

            // ----------------------------------
            // 2. BUAT HEADER MUTASI BARANG
            // ----------------------------------
            $mutasi = MutasiBarang::create([
                'jenis_mutasi' => 'keluar',
                'tanggal_mutasi' => now(),
                'keterangan' => 'Mutasi barang keluar dari Permintaan No. ' . $permintaan->no_permintaan,
                'dicatat_oleh_user_id' => auth()->id(),
                'permintaan_id' => $permintaan->id,
            ]);

            // Generate nomor mutasi (opsional tapi disarankan)
            $mutasi->update([
                'nomor_mutasi' => 'MB-' . str_pad($mutasi->id, 5, '0', STR_PAD_LEFT)
            ]);

            // ----------------------------------
            // 3. LOOP DETAIL PERMINTAAN → DETAIL MUTASI
            // ----------------------------------
            foreach ($permintaan->detailPermintaans as $detail) {

                $barang = Barang::findOrFail($detail->barang_id);

                // Validasi stok cukup
                if ($barang->stok_saat_ini < $detail->jumlah_diminta) {
                    throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi!");
                }

                // Simpan detail mutasi
                DetailMutasiBarang::create([
                    'mutasi_barang_id' => $mutasi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $detail->jumlah_diminta,
                    'catatan' => $detail->keterangan,
                ]);

                // Update stok barang
                $barang->update([
                    'stok_saat_ini' => $barang->stok_saat_ini - $detail->jumlah_diminta
                ]);
            }

            DB::commit();

            return back()->with('success', 'Permintaan berhasil disetujui dan mutasi barang keluar telah dicatat.');

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
            'tanggal_disetujui' => now(),
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
        ->where('pemohon_user_id', Auth::id()) // keamanan: hanya milik sendiri
        ->firstOrFail();

        $permintaan->file_url = $permintaan->file_path
            ? asset('storage/' . $permintaan->file_path)
            : null;

        return Inertia::render('Permintaan/detail', [
            'permintaan' => $permintaan
        ]);
    }


}
