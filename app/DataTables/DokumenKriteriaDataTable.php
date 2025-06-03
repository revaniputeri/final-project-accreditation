<?php

namespace App\DataTables;

use App\Models\DokumenKriteriaModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DokumenKriteriaDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('status_badge', function ($row) {
                $statusClass = [
                    'tervalidasi' => 'success',
                    'revisi' => 'warning',
                    'kosong' => 'secondary',
                    'perlu validasi' => 'info'
                ][$row->status] ?? 'secondary';

                return '<span class="badge bg-' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                $detailUrl = route('dokumen-kriteria.detail_ajax', $row->id_dokumen_kriteria);
                $editUrl = route('dokumen-kriteria.edit_ajax', $row->id_dokumen_kriteria);
                $deleteUrl = route('dokumen-kriteria.confirm_ajax', $row->id_dokumen_kriteria);

                return '
                    <div class="d-flex justify-content-center gap-2" style="white-space: nowrap;">
                        <button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-sm btn-info" style="margin-left: 5px;">
                            <i class="fas fa-info-circle"></i> Detail
                        </button>
                        <button onclick="modalAction(\'' . $editUrl . '\')" class="btn btn-sm btn-primary" style="margin-left: 5px;">
                            <i class="fas fa-edit"></i> Ubah
                        </button>
                        <button onclick="modalAction(\'' . $deleteUrl . '\')" class="btn btn-sm btn-danger" style="margin-left: 5px;">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->editColumn('judul', function ($row) {
                return '<strong>' . $row->judul . '</strong>';
            })
            ->rawColumns(['aksi', 'judul', 'status_badge'])
            ->setRowId('id_dokumen_kriteria');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DokumenKriteriaModel $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['user', 'validator']); // Load relationships

        // Add filter if needed
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        if (request()->has('no_kriteria') && request('no_kriteria') != '') {
            $query->where('no_kriteria', request('no_kriteria'));
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dokumen-kriteria-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id_dokumen_kriteria')->title('ID'),
            Column::make('no_kriteria')->title('No Kriteria'),
            Column::make('versi')->title('Versi'),
            Column::make('user.nama_lengkap')->title('Pembuat'),
            Column::make('judul')->title('Judul'),
            Column::make('status_badge')->title('Status'),
            Column::make('validator.nama_lengkap')->title('Validator'),
            Column::make('komentar')->title('Komentar'),
            Column::make('created_at')->title('Tanggal Dibuat'),
            Column::computed('aksi')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Dokumen_Kriteria_' . date('YmdHis');
    }
}