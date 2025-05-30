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

        // Get latest dokumen per no_kriteria for the user
        $latestDokumen = DokumenKriteriaModel::where('id_user', $user->id_user)
            ->orderByDesc('versi')
            ->first();

        $dokumen = $latestDokumen ? collect([$latestDokumen]) : collect();

        // Pass no_kriteria to DataTable ajax parameters
        $dataTable->with('no_kriteria', $latestDokumen ? $latestDokumen->no_kriteria : null);

        return $dataTable->render('dokumen_kriteria.index', compact('dokumen'));
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
                $data = $request->only(['no_kriteria', 'nama_file', 'keterangan']);

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
            if ($dokumen_pendukung->path_file && Storage::exists($dokumen_pendukung->path_file)) {
                Storage::delete($dokumen_pendukung->path_file);
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
