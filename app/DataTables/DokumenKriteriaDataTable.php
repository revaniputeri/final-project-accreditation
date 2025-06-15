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

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '<button class="btn btn-sm btn-primary preview-btn" data-no-kriteria="' . $row->no_kriteria . '" data-kategori="' . $row->kategori . '">
                            <i class="fas fa-eye"></i> Preview
                        </button>';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = [
                    'tervalidasi' => 'badge-success',
                    'revisi' => 'badge-warning',
                    'perlu validasi' => 'badge-info',
                    'kosong' => 'badge-secondary',
                ];
                return '<span class="badge p-2 ' . ($badgeClass[$row->status] ?? 'badge-secondary') . '">'
                    . strtoupper($row->status) . '</span>';
            })
            ->editColumn('kategori', function ($row) {
                $badgeColor = [
                    'penetapan'     => '#a570fc', // ungu terang
                    'pelaksanaan'   => '#00c292', // hijau toska
                    'evaluasi'      => '#fd7e14', // oranye
                    'pengendalian'  => '#e969a4', // pink
                    'peningkatan'   => '#009efb', // biru muda
                ];

                $color = $badgeColor[$row->kategori] ?? '#6c757d'; // default abu-abu

                return '<span class="badge p-2" style="background-color:' . $color . '; color:white;">'
                    . strtoupper($row->kategori) . '</span>';
            })
            ->editColumn('updated_at', function ($row) {
                return date('d M Y, H:i:s', strtotime($row->updated_at));
            })
            ->editColumn('no_kriteria', function ($row) {
                return '<strong>' . 'Kriteria ' . $row->no_kriteria . '</strong>';
            })
            ->rawColumns(['aksi', 'status', 'kategori', 'no_kriteria', 'updated_at']);
    }

    public function query(DokumenKriteriaModel $model): QueryBuilder
    {
        $subQuery = $model->selectRaw('MAX(versi) as max_versi, no_kriteria, kategori')
            ->groupBy('no_kriteria', 'kategori');

        $query = $model->newQuery()
            ->joinSub($subQuery, 'latest_versions', function ($join) {
                $join->on('dokumen_kriteria.no_kriteria', '=', 'latest_versions.no_kriteria')
                    ->on('dokumen_kriteria.kategori', '=', 'latest_versions.kategori')
                    ->on('dokumen_kriteria.versi', '=', 'latest_versions.max_versi');
            });

        if (!empty($this->no_kriteria)) {
            $query->where('dokumen_kriteria.no_kriteria', $this->no_kriteria);
        }

        if (!empty($this->kategori)) {
            $query->where('dokumen_kriteria.kategori', $this->kategori);
        }

        return $query->select('dokumen_kriteria.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('p_dokumen_kriteria-table')
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

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->searchable(false)->orderable(false),
            Column::make('no_kriteria')->title('No Kriteria'),
            Column::make('judul')->title('Judul'),
            Column::make('kategori')->title('Kategori'),
            Column::make('versi')->title('Versi'),
            Column::make('status')->title('Status'),
            Column::make('updated_at')->title('Terakhir Diupdate'),
            Column::computed('aksi')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'DokumenKriteria_' . date('YmdHis');
    }
}

