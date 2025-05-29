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

        // Hitung versi terakhir untuk user + no_kriteria yang sama
        $versiTerakhir = DokumenKriteriaModel::where('id_user', Auth::id())
            ->where('no_kriteria', $request->no_kriteria)
            ->max('versi');

        $versiBaru = $versiTerakhir ? $versiTerakhir + 1 : 1;

        DokumenKriteriaModel::create([
            'id_user' => Auth::id(),
            'content_html' => $request->content_html,
            'versi' => $versiBaru,
            'status' => 'perlu validasi',
        ]);

        return redirect()->route('dokumen_kriteria.index')->with('success', 'Dokumen berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'content_html' => 'required|string',
            'no_kriteria' => 'required|integer',
        ]);

        $dokumen = DokumenKriteriaModel::findOrFail($id);

        // Update fields
        $dokumen->judul = $request->judul;
        $dokumen->content_html = $request->content_html;
        $dokumen->no_kriteria = $request->no_kriteria;
        // Optionally update versi or status if needed
        $dokumen->save();

        return redirect()->route('dokumen_kriteria.index')->with('success', 'Dokumen berhasil diperbarui.');
    }
}
