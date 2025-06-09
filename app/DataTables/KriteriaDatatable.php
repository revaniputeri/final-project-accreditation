<?php

namespace App\DataTables;

use App\Models\KriteriaModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class KriteriaDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $detailUrl = route('kriteria.detail_ajax', ['no_kriteria' => $row->no_kriteria, 'id_user' => $row->id_user ?? '']);
                $editUrl = route('kriteria.edit_ajax', ['no_kriteria' => $row->no_kriteria, 'id_user' => $row->id_user ?? '']);
                $deleteUrl = route('kriteria.confirm_ajax', ['no_kriteria' => $row->no_kriteria, 'id_user' => $row->id_user ?? '']);

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
            ->editColumn('no_kriteria', function ($row) {
                return '<strong>' . 'Kriteria ' . $row->no_kriteria . '</strong>';
            })
            ->rawColumns(['aksi', 'no_kriteria'])
            ->setRowId('no_kriteria');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(KriteriaModel $model): QueryBuilder
    {
        return $model->newQuery()
            ->selectRaw('kriteria.no_kriteria, MIN(kriteria.id_user) as id_user, COUNT(DISTINCT dokumen_pendukung.id_dokumen_pendukung) as jumlah_dokumen')
            ->leftJoin('user', 'kriteria.id_user', '=', 'user.id_user')
            ->leftJoin('dokumen_pendukung', 'kriteria.no_kriteria', '=', 'dokumen_pendukung.no_kriteria')
            ->groupBy('kriteria.no_kriteria')
            ->orderBy('kriteria.no_kriteria', 'asc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('kriteria-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('kriteria.index'))
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
            Column::make('DT_RowIndex')->title('No')->orderable(false)->searchable(false),
            Column::make('no_kriteria')->title('No Kriteria'),
            Column::make('jumlah_dokumen')->title('Jumlah Dokumen'),
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
        return 'Kriteria_' . date('YmdHis');
    }
}
