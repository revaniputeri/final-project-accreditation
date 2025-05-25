<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use App\Models\LevelModel;
use App\Models\ProfileUser;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        $level = LevelModel::all();
        $id_profile = ProfileUser::with(['user.level'])->get();
        return $dataTable->render('user.index', compact('id_profile', 'level'));
    }

    public function create_ajax()
    {
        $level = LevelModel::all();
        return view('user.create_ajax', ['level' => $level]);
    }

    public function store_ajax(Request $request)
    {
        Log::info('store_ajax called', $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_lengkap' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'id_level' => 'required|string|max:10',
                'nidn' => 'required|string|max:20|unique:profile_user,nidn',
                'nip' => 'required|string|max:20',
                'tempat_lahir' => 'required|string|max:100',
                'gelar_depan' => 'string|max:20',
                'gelar_belakang' => 'string|max:20',
                'pendidikan_terakhir' => 'string|max:50',
                'pangkat' => 'string|max:50',
                'jabatan_fungsional' => 'string|max:100',
                'no_telp' => 'string|max:20',
                'alamat' => 'string|max:100',
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
                $user = UserModel::create([
                    'username' => $request->nidn,
                    'password' => bcrypt($request->nidn),
                    'id_level' => $request->id_level,
                    'created_at' => now(),
                ]);
                $formattedTanggal = Carbon::parse($request->tanggal_lahir)->translatedFormat('d F Y');
                $Profile = ProfileUser::create([
                    'id_user' => $user->id_user,
                    'nama_lengkap' => $request->nama_lengkap,
                    'tempat_tanggal_lahir' => $request->tempat_lahir . ', ' . $formattedTanggal,
                    'nidn' => $request->nidn,
                    'nip' => $request->nip,
                    'gelar_depan' => $request->gelar_depan,
                    'gelar_belakang' => $request->gelar_belakang,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'pangkat' => $request->pangkat,
                    'jabatan_fungsional' => $request->jabatan_fungsional,
                    'alamat' => $request->alamat,
                    'no_telp' => $request->no_telp,
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data level',
                ]);
            }
            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data level berhasil disimpan'
            ]);
        }

        Log::warning('Invalid request in store_ajax');
        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid',
        ], 400);
    }

    public function edit_ajax(string $id)
    {
        $level = LevelModel::all();
        $user = ProfileUser::with(['user.level'])->find($id);
        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }


    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Aturan validasi
            $rules = [
                'nama_lengkap' => 'required|string|max:100',
                'id_level_form' => 'required|string|max:10',
                'nidn' => 'required|string|max:20|unique:profile_user,nidn,' . $id . ',id_profile',
                'nip' => 'required|string|max:20',
                'tempat_tanggal_lahir' => 'required|string|max:100',
                'gelar_depan' => 'nullable|string|max:20',
                'gelar_belakang' => 'nullable|string|max:20',
                'pendidikan_terakhir' => 'nullable|string|max:50',
                'pangkat' => 'nullable|string|max:50',
                'jabatan_fungsional' => 'nullable|string|max:100',
                'no_telp' => 'nullable|string|max:20',
                'alamat' => 'nullable|string|max:100',
            ];

            try {
                // Ambil user profile sesuai $id
                $user = ProfileUser::findOrFail($id);
                \Log::info('User found', ['user' => $user]);

                // Ambil user level terkait
                $userLevel = UserModel::findOrFail($user->id_user);
                \Log::info('UserLevel found', ['userLevel' => $userLevel]);

                // Validasi input
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    \Log::info('Validation failed', ['errors' => $validator->errors()]);
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Validasi gagal.',
                        'msgField' => $validator->errors()
                    ]);
                }

                // Update userLevel
                $userLevel->update([
                    'id_level' => $request->id_level_form,
                    'updated_at' => now(),
                ]);

                // Update profile user
                $user->update([
                    'nama_lengkap' => $request->nama_lengkap,
                    'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
                    'nidn' => $request->nidn,
                    'nip' => $request->nip,
                    'gelar_depan' => $request->gelar_depan,
                    'gelar_belakang' => $request->gelar_belakang,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'pangkat' => $request->pangkat,
                    'jabatan_fungsional' => $request->jabatan_fungsional,
                    'alamat' => $request->alamat,
                    'no_telp' => $request->no_telp,
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data User berhasil diupdate'
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error during update: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());

                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        // Jika bukan AJAX, redirect ke homepage
        return redirect('/');
    }




    public function confirm_ajax(string $id)
    {
        $user = ProfileUser::find($id);
        return view('user.confirm_ajax', ['user' => $user]);
    }


    public function delete_ajax(Request $request, string $id)
    {
        $profile = ProfileUser::find($id);
        $user = UserModel::find($profile->id_user);
        $profile->delete();
        $user->delete();
        if ($user && $profile) {
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

    public function detail_ajax(string $id)
    {
        $user = ProfileUser::with(['user.level'])->find($id);
        return view('user.detail_ajax', ['user' => $user]);
    }


    public function import()
    {
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_user' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_user');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $skippedData = [];
            $successData = [];
            $existingNIDNs = ProfileUser::pluck('nidn')->toArray();
            $existingUsernames = UserModel::pluck('username')->toArray();
            $insertDataProfile = [];
            $nidnInFile = []; // Untuk track duplikasi dalam file

            foreach ($data as $row => $values) {
                if ($row <= 1)
                    continue; // Skip header

                $nidn = trim($values['C'] ?? '');

                // Validasi dasar NIDN
                if (empty($nidn)) {
                    $skippedData[] = "Baris $row: NIDN kosong, dilewati.";
                    continue;
                }

                // Cek duplikasi dalam database ATAU dalam file
                if (
                    in_array($nidn, $existingNIDNs) ||
                    in_array($nidn, $existingUsernames) ||
                    in_array($nidn, $nidnInFile)
                ) {
                    $skippedData[] = "Baris $row: NIDN $nidn sudah ada (di database/file), dilewati.";
                    continue;
                }

                // Buat user baru
                try {
                    $createdUser = UserModel::create([
                        'id_level' => $values['L'] ?? null,
                        'username' => $nidn,
                        'password' => bcrypt($nidn),
                    ]);

                    $insertDataProfile[] = [
                        'id_user' => $createdUser->id_user,
                        'nama_lengkap' => $values['A'] ?? null,
                        'nidn' => $nidn,
                        'tempat_tanggal_lahir' => $values['B'],
                        'nip' => $values['D'],
                        'gelar_depan' => $values['E'],
                        'gelar_belakang' => $values['F'],
                        'pendidikan_terakhir' => $values['G'],
                        'pangkat' => $values['H'],
                        'jabatan_fungsional' => $values['I'],
                        'no_telp' => $values['J'],
                        'alamat' => $values['K'],
                    ];

                    $nidnInFile[] = $nidn; // Catat NIDN yang sudah diproses
                    $successData[] = "Baris $row: NIDN $nidn berhasil diimport.";

                } catch (\Exception $e) {
                    $skippedData[] = "Baris $row: Gagal membuat user - " . $e->getMessage();
                }
            }

            // Insert semua profile sekaligus
            if (!empty($insertDataProfile)) {
                ProfileUser::insert($insertDataProfile);
            }

            $response = [
                'status' => !empty($insertDataProfile),
                'alert' => !empty($insertDataProfile) ? 'success' : 'error',
                'message' => !empty($insertDataProfile)
                    ? 'Data berhasil diimport.'
                    : 'Tidak ada data valid yang bisa diimport.',
                'info' => array_merge($successData, $skippedData),
                'success_count' => count($successData),
                'skipped_count' => count($skippedData),
            ];

            return response()->json($response, !empty($insertDataProfile) ? 200 : 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function export_excel(Request $request)
    {
        $query = ProfileUser::with(['user.level'])->
            orderBy('id_profile');

        if (request()->has('id_level') && request('id_level') != '') {
            $query->whereHas('user', function ($q) {
                $q->where('id_level', request('id_level'));
            });
        }

        $users = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIDN');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'Level');
        $sheet->setCellValue('E1', 'Nama Lengkap');
        $sheet->setCellValue('F1', 'Tempat Tanggal Lahir');
        $sheet->setCellValue('G1', 'No Telp');
        $sheet->setCellValue('H1', 'Alamat');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        foreach ($users as $index => $profile) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $profile->nidn);
            $sheet->setCellValue('C' . $row, $profile->user->username ?? '');
            $sheet->setCellValue('D' . $row, $profile->user->level->nama_level ?? '');
            $sheet->setCellValue('E' . $row, $profile->nama_lengkap);
            $sheet->setCellValue('F' . $row, $profile->tempat_tanggal_lahir);
            $sheet->setCellValue('G' . $row, $profile->no_telp);
            $sheet->setCellValue('H' . $row, $profile->alamat);
            $row++;
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data User");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User' . date("Y-m-d H:i:s") . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf(Request $request)
    {
        $query = ProfileUser::with(['user.level'])
            ->orderBy('id_profile');

        if (request()->has('id_level') && request('id_level') != '') {
            $query->whereHas('user', function ($q) {
                $q->where('id_level', request('id_level'));
            });
        }

        $user = $query->get();

        $pdf = Pdf::loadView('user.export_pdf', [
            'user' => $user
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data User' . date('d-m-Y H:i:s') . '.pdf');
    }
}
