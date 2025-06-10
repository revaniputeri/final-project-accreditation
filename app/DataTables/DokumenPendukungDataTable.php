<?php

namespace App\DataTables;

use App\Models\DokumenPendukungModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DokumenPendukungDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn() // Add this line to add row index column
            ->addColumn('user_full_name', function ($row) {
                return $row->user && $row->user->profile ? $row->user->profile->nama_lengkap : '-';
            })
->addColumn('aksi', function ($row) {
    $kategori = $this->kategori ?? '';
    $detailUrl = route('dokumen_kriteria.detail_ajax', $row->id_dokumen_pendukung);
    $editUrl = route('dokumen_kriteria.edit_ajax', $row->id_dokumen_pendukung);
    $deleteUrl = route('dokumen_kriteria.confirm_ajax', $row->id_dokumen_pendukung);

    return '
        <div class="d-flex justify-content-center gap-2" style="white-space: nowrap;">
            <button onclick="copyPath(\'' . $row->path_file . '\')" class="btn btn-sm btn-warning" style="margin-left: 5px;">
                <i class="fas fa-copy"></i> Copy Path
            </button>
            <button onclick="modalAction(\'' . $detailUrl . '\', \'' . $kategori . '\')" class="btn btn-sm btn-info" style="margin-left: 5px;">
                <i class="fas fa-info-circle"></i> Detail
            </button>
            <button onclick="modalAction(\'' . $editUrl . '\', \'' . $kategori . '\')" class="btn btn-sm btn-primary" style="margin-left: 5px;">
                <i class="fas fa-edit"></i> Ubah
            </button>
            <button onclick="modalAction(\'' . $deleteUrl . '\', \'' . $kategori . '\')" class="btn btn-sm btn-danger" style="margin-left: 5px;">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    ';
})
            ->editColumn('nama_file', function ($row) {
                return '<strong>' . $row->nama_file . '</strong>';
            })
            ->editColumn('path_file', function ($row) {
                return '<a href="' . asset('storage/dokumen_pendukung/' . basename($row->path_file)) . '" target="_blank">' . basename($row->path_file) . '</a>';
            })
            ->rawColumns(['aksi', 'nama_file', 'path_file']) // Allow HTML in these columns
            ->setRowId('id_dokumen_pendukung');
    }

    /**
     * Get the query source of dataTable.
     */
    protected $no_kriteria;
    protected $kategori;

    public function with(array|string $key, mixed $value = null): static
    {
        if (is_array($key)) {
            $this->no_kriteria = $key['no_kriteria'] ?? null;
            $this->kategori = $key['kategori'] ?? null;
        } else {
            if ($key === 'no_kriteria') {
                $this->no_kriteria = $value;
            }
            if ($key === 'kategori') {
                $this->kategori = $value;
            }
        }
        return $this;
    }

    public function query(DokumenPendukungModel $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['user.profile']);

        if ($this->no_kriteria) {
            $query->where('no_kriteria', $this->no_kriteria);
        }

        if ($this->kategori) {
            $query->where('kategori', $this->kategori);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dokumen-pendukung-table')
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
            Column::computed('DT_RowIndex')->title('No')->searchable(false)->orderable(false),
            Column::make('nama_file')->title('Nama File'),
            Column::make('keterangan')->title('Keterangan'),
            Column::make('user_full_name')->title('Diunggah Oleh'),
            Column::computed('aksi')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DokumenPendukung_' . date('YmdHis');
    }
}
