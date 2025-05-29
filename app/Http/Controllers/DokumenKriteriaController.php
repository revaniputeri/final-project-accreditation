<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenKriteriaModel;
use Illuminate\Support\Facades\Auth;

class DokumenKriteriaController extends Controller
{
    public function index()
    {
        /** @var UserModel|null $user */
        $user = Auth::user();

        if ($user->level->nama_level === 'ADM') {
            // Tampilkan semua dokumen kriteria dari semua user
            $dokumen = DokumenKriteriaModel::orderByDesc('created_at')->get();
        } else {
            // Hanya tampilkan milik user itu sendiri
            $dokumen = DokumenKriteriaModel::where('id_user', $user->id_user)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('dokumen_kriteria.index', compact('dokumen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content_html' => 'required|string',
        ]);

        // This method is not used in this page as per user instruction
        // You can remove or keep it as is

        return redirect()->route('dokumen_kriteria.index')->with('success', 'Dokumen berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content_html' => 'required|string',
        ]);

        $dokumenLama = DokumenKriteriaModel::findOrFail($id);

        // Hitung versi terakhir untuk user + no_kriteria yang sama
        $versiTerakhir = DokumenKriteriaModel::where('id_user', Auth::id())
            ->where('no_kriteria', $dokumenLama->no_kriteria)
            ->max('versi');

        $versiBaru = $versiTerakhir ? $versiTerakhir + 1 : 1;

        // Buat versi baru dengan judul dan no_kriteria dari versi lama
        DokumenKriteriaModel::create([
            'id_user' => Auth::id(),
            'judul' => $dokumenLama->judul,
            'content_html' => $request->content_html,
            'no_kriteria' => $dokumenLama->no_kriteria,
            'versi' => $versiBaru,
            'status' => 'perlu validasi',
        ]);

        return redirect()->route('dokumen_kriteria.index')->with('success', 'Dokumen berhasil diperbarui dan versi baru dibuat.');
    }
}
