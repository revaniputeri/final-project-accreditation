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

    public function create_ajax()
    {
        return view('kriteria.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        Log::info('store_ajax called', $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'no_kriteria' => 'required|string|max:10',
                'id_user' => 'required|exists:user,id_user',
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
                $kriteria = KriteriaModel::create([
                    'no_kriteria' => $request->no_kriteria,
                    'id_user' => $request->id_user,
                ]);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data kriteria berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data kriteria',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid',
        ], 400);
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
        $kriteria = KriteriaModel::where('no_kriteria', $no_kriteria)
                                ->where('id_user', $id_user)
                                ->first();
        if ($kriteria) {
            $kriteria->delete();
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
        $kriteria = KriteriaModel::with(['user', 'dokumenKriteria', 'dokumenPendukung'])
                                ->where('no_kriteria', $no_kriteria)
                                ->where('id_user', $id_user)
                                ->first();
        return view('kriteria.detail_ajax', ['kriteria' => $kriteria]);
    }

    public function export_excel()
    {
        $kriteria = KriteriaModel::with(['user'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Kriteria');
        $sheet->setCellValue('C1', 'Nama User');
        $sheet->setCellValue('D1', 'Jumlah Dokumen Kriteria');
        $sheet->setCellValue('E1', 'Jumlah Dokumen Pendukung');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $row = 2;
        foreach ($kriteria as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->no_kriteria);
            $sheet->setCellValue('C' . $row, $item->user->username ?? '');
            $sheet->setCellValue('D' . $row, $item->dokumenKriteria->count());
            $sheet->setCellValue('E' . $row, $item->dokumenPendukung->count());
            $row++;
        }

        foreach (range('A', 'E') as $columnID) {
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
        $kriteria = KriteriaModel::with(['user', 'dokumenKriteria', 'dokumenPendukung'])->get();

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
}