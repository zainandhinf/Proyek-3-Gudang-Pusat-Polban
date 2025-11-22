<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
}
