<?php

namespace App\Http\Controllers;

use App\DataTables\KriteriaDataTable;
use App\Models\KriteriaModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\DokumenKriteriaModel;
use App\Models\DokumenPendukungModel;
use App\Models\ProfileUser;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function index(KriteriaDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();

        // Get latest dokumen per no_kriteria for the user
        $latestDokumen = KriteriaModel::where('id_user', $user->id_user)
            ->orderByDesc('no_kriteria')
            ->first();

        $kriteria = $latestDokumen ? collect([$latestDokumen]) : collect();

        // Pass no_kriteria to DataTable ajax parameters
        $dataTable->with('no_kriteria', $latestDokumen ? $latestDokumen->no_kriteria : null);

        return $dataTable->render('kriteria.index', compact('kriteria'));
    }

    public function showDokumenPendukung($no_kriteria)
    {
        $dokumenPendukung = DokumenPendukungModel::where('no_kriteria', $no_kriteria)
            ->get()
            ->groupBy('kategori');

        return view('landing_page.kriteria', compact('dokumenPendukung', 'no_kriteria'));
    }

    public function create_ajax()
    {
        $users = ProfileUser::select('id_profile', 'nama_lengkap')->get();
        return view('kriteria.create_ajax', compact('users'));
    }

    public function store_ajax(Request $request)
    {
        $rules = [
            'no_kriteria' => 'required|string|max:10',
            'selected_users' => 'required|array|min:1|max:2',
            'selected_users.*' => 'exists:user,id_user',
        ];
        $messages = [
            'selected_users.required' => 'Pilih minimal 1 user.',
            'selected_users.max' => 'Maksimal 2 user.',
            'selected_users.*.exists' => 'User tidak valid.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // Cek user duplikat di array
        $selectedUsers = $request->input('selected_users', []);
        $uniqueUsers = array_unique($selectedUsers);
        if (count($selectedUsers) !== count($uniqueUsers)) {
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'User tidak boleh sama.',
            ]);
        }

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => $validator->errors()->first(),
                'msgField' => $validator->errors()->first(),
            ]);
        }

        $no_kriteria = $request->no_kriteria;
        if (is_string($no_kriteria)) {
            preg_match('/\d+$/', $no_kriteria, $matches);
            $no_kriteria = $matches ? (int)$matches[0] : 1;
        }

        $users = $request->selected_users;

        try {
            foreach ($users as $userId) {                
                $kriteria = KriteriaModel::where('id_user', $userId)
                    ->where('no_kriteria', $no_kriteria)
                    ->whereNull('deleted_at')
                    ->first();
                if (!$kriteria) {
                    KriteriaModel::create([
                        'no_kriteria' => $no_kriteria,
                        'id_user' => $userId,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'User sudah ada di kriteria ini.',
                    ]);
                }

                $categories = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];

                foreach ($categories as $category) {
                    DokumenKriteriaModel::create([
                        'no_kriteria' => $no_kriteria,
                        'versi' => 1,
                        'judul' => 'Dokumen Kriteria ' . $no_kriteria,
                        'kategori' => $category,
                        'content_html' => '<p>Konten default untuk kriteria ' . $no_kriteria . ' dengan kategori ' . $category . '</p>',
                        'status' => 'kosong',
                        'id_validator' => null,
                        'komentar' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Berhasil menambah kriteria.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'Gagal menyimpan data kriteria: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit_ajax($no_kriteria, $id_user)
    {
        $kriteria = KriteriaModel::where('no_kriteria', $no_kriteria)
            ->where('id_user', $id_user)
            ->first();
        $users = UserModel::all();
        return view('kriteria.edit_ajax', ['kriteria' => $kriteria, 'users' => $users]);
    }

    public function update_ajax(Request $request, $no_kriteria, $id_user)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'no_kriteria' => 'required|string|max:10',
                'id_user' => 'required|exists:user,id_user',
            ];

            try {
                $kriteria = KriteriaModel::where('no_kriteria', $no_kriteria)
                    ->where('id_user', $id_user)
                    ->firstOrFail();

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Validasi gagal.',
                        'msgField' => $validator->errors()
                    ]);
                }

                $kriteria->update([
                    'no_kriteria' => $request->no_kriteria,
                    'id_user' => $request->id_user,
                ]);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data kriteria berhasil diupdate'
                ]);
            } catch (\Throwable $e) {
                Log::error('Error during update: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return redirect('/');
    }

    public function confirm_ajax($no_kriteria, $id_user)
    {
        $kriteria = KriteriaModel::where('no_kriteria', $no_kriteria)
            ->where('id_user', $id_user)
            ->first();
        return view('kriteria.confirm_ajax', ['kriteria' => $kriteria]);
    }

    public function delete_ajax(Request $request, $no_kriteria, $id_user)
    {
        $dokumen_pendukung = DokumenPendukungModel::where('no_kriteria', $no_kriteria)
            ->whereNull('deleted_at');
        $dokumen_kriteria = DokumenKriteriaModel::where('no_kriteria', $no_kriteria)
            ->whereNull('deleted_at');
        $kriteria = KriteriaModel::where('no_kriteria', $no_kriteria)
            ->whereNull('deleted_at');
        $dokumen_pendukung->delete();
        $dokumen_kriteria->delete();
        if ($kriteria) {
            $kriteria->delete();
            $dokumen_pendukung->delete();
            $dokumen_kriteria->delete();
        }
        if ($kriteria || $dokumen_pendukung || $dokumen_kriteria) {
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function detail_ajax($no_kriteria, $id_user)
    {
        try {
            $users = KriteriaModel::join('profile_user', 'kriteria.id_user', '=', 'profile_user.id_user')
                ->where('kriteria.no_kriteria', $no_kriteria)
                ->pluck('profile_user.nama_lengkap')
                ->toArray();

            $kriteria = KriteriaModel::select('kriteria.*', DB::raw('COUNT(DISTINCT dokumen_pendukung.id_dokumen_pendukung) as jumlah_dokumen'))
                ->leftJoin('dokumen_pendukung', function ($join) {
                    $join->on('kriteria.no_kriteria', '=', 'dokumen_pendukung.no_kriteria')
                        ->whereNull('dokumen_pendukung.deleted_at');
                })
                ->where('kriteria.no_kriteria', $no_kriteria)
                ->where('kriteria.id_user', $id_user)
                ->whereNull('kriteria.deleted_at')
                ->groupBy('kriteria.no_kriteria', 'kriteria.id_user')
                ->first();

            if (!$kriteria) {
                return response()->view('kriteria.detail_ajax', [
                    'kriteria' => null,
                    'user_list' => $users
                ]);
            }

            return view('kriteria.detail_ajax', [
                'kriteria' => $kriteria,
                'user_list' => $users
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in detail_ajax: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail kriteria'
            ], 500);
        }
    }

    public function export_excel()
    {
        $kriteria = KriteriaModel::with(['profile_user', 'dokumenPendukung'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Kriteria');
        $sheet->setCellValue('C1', 'Nama User');
        $sheet->setCellValue('D1', 'Jumlah Dokumen Pendukung');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $row = 2;
        $kriteria = $kriteria->groupBy('no_kriteria')->map(function ($group) {
            $first = $group->first();
            $namaLengkap = $group->pluck('profile_user.nama_lengkap')->filter()->join(', ');
            $first->setRelation('profile_user', (object)['nama_lengkap' => $namaLengkap]);
            return $first;
        });

        foreach ($kriteria as $index => $item) {
            $sheet->setCellValue('A' . $row, $row - 1);
            $sheet->setCellValue('B' . $row, 'Kriteria ' . $item->no_kriteria);
            $sheet->setCellValue('C' . $row, $item->profile_user->nama_lengkap ?: '-');
            $sheet->setCellValue('D' . $row, $item->dokumenPendukung->count());
            $row++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Kriteria ' . date("Y-m-d H:i:s") . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $kriteria = KriteriaModel::with(['user', 'profile_user', 'dokumenKriteria', 'dokumenPendukung'])
            ->get()
            ->groupBy('no_kriteria')
            ->map(function ($group) {
                $first = $group->first();
                $namaLengkap = $group->pluck('profile_user.nama_lengkap')->filter()->join(', ');
                $first->setRelation('profile_user', (object)['nama_lengkap' => $namaLengkap]);
                return $first;
            });

        $pdf = Pdf::loadView('kriteria.export_pdf', [
            'kriteria' => $kriteria
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Data Kriteria ' . date('d-m-Y H:i:s') . '.pdf');
    }

    public function getLastNumber()
    {
        $lastKriteria = KriteriaModel::orderBy('no_kriteria', 'desc')->first();

        if ($lastKriteria) {
            return response()->json(['last_number' => $lastKriteria->no_kriteria]);
        }

        return response()->json(['last_number' => null]);
    }

    public function getUsers()
    {
        try {
            $users = ProfileUser::select(
                'profile_user.id_profile',
                'profile_user.nama_lengkap',
                'profile_user.id_user'
            )
                ->join('user', 'profile_user.id_user', '=', 'user.id_user')
                ->where('user.id_level', 2)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('kriteria')
                        ->whereRaw('kriteria.id_user = profile_user.id_user')
                        ->whereNull('kriteria.deleted_at'); // Masih aktif
                })
                ->orderBy('profile_user.nama_lengkap', 'asc')
                ->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
