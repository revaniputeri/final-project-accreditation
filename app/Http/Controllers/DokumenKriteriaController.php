<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenKriteriaModel;
use Illuminate\Support\Facades\Auth;
use App\DataTables\DokumenPendukungDataTable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\DokumenPendukungModel;
use Illuminate\Support\Facades\Storage;

class DokumenKriteriaController extends Controller
{
    public function index(DokumenPendukungDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();

        // Get user's assigned no_kriteria(s)
        $userNoKriteria = \App\Models\KriteriaModel::where('id_user', $user->id_user)->pluck('no_kriteria')->toArray();

        // Get kategori list for user's no_kriteria(s)
        $kategoriList = DokumenKriteriaModel::whereIn('no_kriteria', $userNoKriteria)
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori')
            ->toArray();

        // Get dokumen_kriteria grouped by kategori for user's no_kriteria(s)
        $dokumenGrouped = DokumenKriteriaModel::whereIn('no_kriteria', $userNoKriteria)
            ->orderBy('kategori')
            ->orderByDesc('versi')
            ->get()
            ->groupBy('kategori');

        // Default kategori to first in list or null
        $selectedKategori = request()->query('kategori', $kategoriList[0] ?? null);

        // Filter dokumen for selected kategori
        $dokumen = $dokumenGrouped->get($selectedKategori, collect());

        // Pass no_kriteria and kategori to DataTable
        $dataTable->with([
            'no_kriteria' => $userNoKriteria[0] ?? null,
            'kategori' => $selectedKategori,
        ]);

        return $dataTable->render('dokumen_kriteria.index', compact('dokumen', 'kategoriList', 'selectedKategori', 'dokumenGrouped'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content_html' => 'required|string',
        ]);

        $dokumenLama = DokumenKriteriaModel::findOrFail($id);

        if ($dokumenLama->status === 'tervalidasi') {
            return redirect()->route('dokumen_kriteria.index')->with('swal_error', 'Dokumen yang sudah tervalidasi tidak dapat diedit.');
        }

        // Function to strip outer div with class "editor-a4-body"
        $stripOuterDiv = function ($html) {
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $xpath = new \DOMXPath($doc);
            $divs = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " editor-a4-body ")]');
            if ($divs->length > 0) {
                $div = $divs->item(0);
                $innerHTML = '';
                foreach ($div->childNodes as $child) {
                    $innerHTML .= $doc->saveHTML($child);
                }
                return $innerHTML;
            }
            return $html;
        };

        if ($request->input('action') === 'save') {
            // Strip outer div before saving
            $cleanContent = $stripOuterDiv($request->content_html);
            $dokumenLama->content_html = $cleanContent;
            $dokumenLama->save();

            return redirect()->route('dokumen_kriteria.index')->with('success', 'Dokumen berhasil disimpan.');
        } elseif ($request->input('action') === 'submit') {
            // Strip outer div before creating new version
            $cleanContent = $stripOuterDiv($request->content_html);

            $versiTerakhir = DokumenKriteriaModel::select('dokumen_kriteria.*')
                ->join('kriteria', 'dokumen_kriteria.no_kriteria', '=', 'kriteria.no_kriteria')
                ->where('kriteria.id_user', Auth::id())
                ->where('dokumen_kriteria.no_kriteria', $dokumenLama->no_kriteria)
                ->where('dokumen_kriteria.kategori', $dokumenLama->kategori)
                ->max('dokumen_kriteria.versi');

            $versiBaru = $versiTerakhir ? $versiTerakhir + 1 : 1;

            DokumenKriteriaModel::create([
                'judul' => $dokumenLama->judul,
                'content_html' => $cleanContent,
                'no_kriteria' => $dokumenLama->no_kriteria,
                'kategori' => $dokumenLama->kategori,
                'versi' => $versiBaru,
                'status' => 'perlu validasi',
            ]);

            return redirect()->route('dokumen_kriteria.index')->with('success', 'Dokumen berhasil disubmit dan versi baru dibuat.');
        }

        return redirect()->route('dokumen_kriteria.index')->with('error', 'Aksi tidak dikenali.');
    }

    // CRUD Dokumen Pendukung

    public function create_ajax(Request $request)
    {
        $no_kriteria = $request->query('no_kriteria');
        return view('dokumen_kriteria.create_ajax', compact('no_kriteria'));
    }

    public function store_ajax(Request $request)
    {
        Log::info('store_ajax called', $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'no_kriteria' => 'required|string|exists:dokumen_kriteria,no_kriteria',
                'kategori' => 'required|string',
                'nama_file' => 'required|string|max:255',
                'Keterangan' => 'required|string|max:100',
                'dokumen_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                $file = $request->file('dokumen_pendukung');
                $path = $file->store('public/dokumen_pendukung');
                $filename = basename($path);

                DokumenPendukungModel::create([
                    'no_kriteria' => $request->no_kriteria,
                    'kategori' => $request->kategori,
                    'id_user' => Auth::id(),
                    'nama_file' => $request->nama_file,
                    'path_file' => $filename,
                    'keterangan' => $request->Keterangan,
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data dokumen pendukung',
                ]);
            }

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data dokumen pendukung berhasil disimpan'
            ]);
        }

        Log::warning('Invalid request in store_ajax');
        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function edit_ajax($id)
    {
        $dokumen_pendukung = DokumenPendukungModel::findOrFail($id);
        return view('dokumen_kriteria.edit_ajax', compact('dokumen_pendukung'));
    }

    public function update_ajax(Request $request, $id)
    {
        Log::info('update_ajax request data:', $request->all());
        Log::info('update_ajax request all input:', $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'no_kriteria' => 'required|string|exists:dokumen_kriteria,no_kriteria',
                'kategori' => 'required|string',
                'nama_file' => 'required|string|max:255',
                'keterangan' => 'required|string|max:100',
                'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Validation failed in update_ajax', $validator->errors()->toArray());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $dokumen_pendukung = DokumenPendukungModel::findOrFail($id);
            try {
                $data = $request->only(['no_kriteria', 'kategori', 'nama_file', 'keterangan']);

                if ($request->hasFile('dokumen_pendukung')) {
                    if ($dokumen_pendukung->path_file && Storage::exists('public/dokumen_pendukung/' . $dokumen_pendukung->path_file)) {
                        Storage::delete('public/dokumen_pendukung/' . $dokumen_pendukung->path_file);
                    }
                    $file = $request->file('dokumen_pendukung');
                    $path = $file->store('public/dokumen_pendukung');
                    $filename = basename($path);
                    $data['path_file'] = $filename;
                }

                $dokumen_pendukung->update($data);

                // Ensure id_user is set to current user if not present
                if (!$dokumen_pendukung->id_user) {
                    $dokumen_pendukung->id_user = Auth::id();
                    $dokumen_pendukung->save();
                }

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data dokumen pendukung berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data dokumen pendukung',
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function confirm_ajax($id)
    {
        $dokumen_pendukung = DokumenPendukungModel::findOrFail($id);
        return view('dokumen_kriteria.confirm_ajax', compact('dokumen_pendukung'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $dokumen_pendukung = DokumenPendukungModel::findOrFail($id);
        try {
            if ($dokumen_pendukung->path_file && Storage::exists('public/dokumen_pendukung/' . $dokumen_pendukung->path_file)) {
                Storage::delete('public/dokumen_pendukung/' . $dokumen_pendukung->path_file);
            }
            $dokumen_pendukung->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in delete_ajax: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function detail_ajax($id)
    {
        $dokumen_pendukung = DokumenPendukungModel::findOrFail($id);
        return view('dokumen_kriteria.detail_ajax', compact('dokumen_pendukung'));
    }
}
