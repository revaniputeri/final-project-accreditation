<?php

namespace App\DataTables;

use App\Models\ProfileUser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('nama_level', function ($row) {
                return '<strong>' . optional($row->user->level)->nama_level ?? '-' . '</strong>';
            })
            ->addColumn('aksi', function ($row) {
                $detailUrl = route('user.detail_ajax', $row->id_profile);
                $editUrl = route('user.edit_ajax', $row->id_profile);
                $deleteUrl = route('user.confirm_ajax', $row->id_profile);

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
            ->editColumn('nidn', function ($row) {
                return '<strong>' . $row->nidn . '</strong>';
            })
            
            ->rawColumns(['aksi', 'nidn','nama_level']) // Important: allow HTML in aksi column
            ->setRowId('id_level');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProfileUser $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['user.level']);
        ;
        // Add filter if needed, e.g. by level_kode or other fields
        if (request()->has('id_level') && request('id_level') != '') {
            $query->whereHas('user', function ($q) {
            $q->where('id_level', request('id_level'));
        });
        }
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
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
            Column::make('id_profile')->title('ID Profile'),
            Column::make('id_user')->title('ID User'),
            Column::make('nidn')->title('NIDN'),
            Column::make('nama_level')->title('Level'),
            Column::make('nama_lengkap')->title('Nama Lengkap'),
            Column::make('tempat_tanggal_lahir')->title('Tempat Tanggal Lahir'),
            Column::make('no_telp')->title('No Telfon'),
            Column::make('alamat')->title('Alamat'),
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
        return 'User_' . date('YmdHis');
    }
}

