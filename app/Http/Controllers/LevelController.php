<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\LevelDataTable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class LevelController extends Controller
{
    public function index(LevelDataTable $dataTable)
    {
        $id_level = LevelModel::all();
        return $dataTable->render('level.index', compact('id_level'));
    }

    public function create_ajax()
    {
        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        Log::info('store_ajax called', $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kode_level' => 'required|string|unique:level,kode_level',
                'nama_level' => 'required|string|max:100',
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
                LevelModel::create([
                    'kode_level' => $request->kode_level,
                    'nama_level' => $request->nama_level,
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
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function edit_ajax(string $id)
    {
        $level = LevelModel::find($id);
        return view('level.edit_ajax', ['level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kode_level' => 'required|string|max:255',
                'nama_level' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $level = LevelModel::find($id);
            if ($level) {
                $level->update($request->all());
                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data level berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $level = LevelModel::find($id);
        return view('level.confirm_ajax', ['level' => $level]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        $level = LevelModel::find($id);
        $level->delete();
        if ($level) {
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
        $level = LevelModel::find($id);
        return view('level.detail_ajax', ['level' => $level]);
    }

    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_level' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_level');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $insertData = [];
            $skippedData = [];
            $errors = [];

            $existingCodes = LevelModel::pluck('kode_level')->toArray();

            foreach ($data as $row => $values) {
                if ($row == 1) continue;

                if (in_array($values['A'], $existingCodes)) {
                    $skippedData[] = "Baris $row: Level dengan kode {$values['A']} sudah ada dan akan diabaikan";
                    continue;
                }

                $validator = Validator::make([
                    'kode_level' => $values['A'],
                    'nama_level' => $values['B'],
                ], [
                    'kode_level' => 'required|string|max:255',
                    'nama_level' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'kode_level' => $values['A'],
                    'nama_level' => $values['B'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $existingCodes[] = $values['A'];
            }

            $allMessages = array_merge($skippedData, $errors);

            if (empty($insertData)) {
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Tidak ada data baru yang valid untuk diimport',
                    'info' => $allMessages
                ], 422);
            }

            $insertedCount = LevelModel::insertOrIgnore($insertData);

            $response = [
                'status' => true,
                'alert' => 'success',
                'message' => 'Import data berhasil',
                'inserted_count' => $insertedCount,
                'skipped_count' => count($skippedData),
                'info' => $allMessages
            ];

            if (!empty($errors)) {
                $response['error_count'] = count($errors);
            }

            $response['alert'] = 'success';
            return response()->json($response, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export_excel(Request $request)
    {
        $query = LevelModel::select('id_level','kode_level', 'nama_level', 'created_at', 'updated_at')
            ->orderBy('id_level');

        if ($request->has('id_level') && $request->id_level != '') {
            $query->where('id_level', $request->id_level);
        }

        $levels = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Level');
        $sheet->setCellValue('C1', 'Kode Level');
        $sheet->setCellValue('D1', 'Nama Level');
        $sheet->setCellValue('E1', 'Created At');
        $sheet->setCellValue('F1', 'Updated At');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($levels as $level) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $level->id_level);
            $sheet->setCellValue('C' . $row, $level->kode_level);
            $sheet->setCellValue('D' . $row, $level->nama_level);
            $sheet->setCellValue('E' . $row, $level->created_at);
            $sheet->setCellValue('F' . $row, $level->updated_at);
            $row++;
            $no++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data Level");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level ' . date("Y-m-d H:i:s") . '.xlsx';

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
        $query = LevelModel::select('id_level', 'kode_level', 'nama_level', 'created_at', 'updated_at')
            ->orderBy('id_level');

        if ($request->has('id_level') && $request->id_level != '') {
            $query->where('id_level', $request->id_level);
        }

        $levels = $query->get();

        $pdf = Pdf::loadView('level.export_pdf', [
            'levels' => $levels
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Level ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
