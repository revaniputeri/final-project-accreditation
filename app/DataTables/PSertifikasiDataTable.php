<?php

namespace App\DataTables;

use App\Models\PSertifikasiModel;
use App\Models\ProfileUser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class PSertifikasiDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $isDos = $user->hasRole('DOS');
        $isAdm = $user->hasRole('ADM');
        $isAng = $user->hasRole('ANG');

        return (new EloquentDataTable($query))
            ->addColumn('aksi', function ($row) use ($user, $isDos, $isAdm) {
                $buttons = [];
                $detailUrl = route('p_sertifikasi.detail_ajax', $row->id_sertifikasi);

                $buttons[] = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-sm btn-info" style="margin-left: 5px;">
                    <i class="fas fa-info-circle"></i> Detail
                </button>';

                if ($isDos) {
                    $validasiUrl = route('p_sertifikasi.validasi_ajax', $row->id_sertifikasi);
                    $buttons[] = '<button onclick="modalAction(\'' . $validasiUrl . '\')" class="btn btn-sm btn-warning" style="margin-left: 5px;">
                        <i class="fas fa-check-circle"></i> Validasi
                    </button>';
                }

                if ($isDos || $isAdm) {
                    $editUrl = route('p_sertifikasi.edit_ajax', $row->id_sertifikasi);
                    $deleteUrl = route('p_sertifikasi.confirm_ajax', $row->id_sertifikasi);

                    $buttons[] = '<button onclick="modalAction(\'' . $editUrl . '\')" class="btn btn-sm btn-primary" style="margin-left: 5px;">
                        <i class="fas fa-edit"></i> Ubah
                    </button>';

                    $buttons[] = '<button onclick="modalAction(\'' . $deleteUrl . '\')" class="btn btn-sm btn-danger" style="margin-left: 5px;">
                        <i class="fas fa-trash"></i> Hapus
                    </button>';
                }

                return '<div class="d-flex justify-content-center gap-2" style="white-space: nowrap;">' .
                    implode('', $buttons) .
                    '</div>';
            })
            ->addColumn('nama_lengkap', function ($row) use ($isDos) {
                return $isDos ? '-' : ($row->user->profile->nama_lengkap ?? '-');
            })
            ->editColumn('status', function ($row) {
                $badgeClass = [
                    'tervalidasi' => 'badge-success',
                    'perlu validasi' => 'badge-warning',
                    'tidak valid' => 'badge-danger'
                ];
                return '<span class="badge p-2 ' . ($badgeClass[$row->status] ?? 'badge-secondary') . '">'
                    . strtoupper($row->status) . '</span>';
            })
            ->editColumn('sumber_data', function ($row) {
                $badgeClass = [
                    'p3m' => 'badge-primary',
                    'dosen' => 'badge-secondary'
                ];
                return '<span class="badge p-2 ' . ($badgeClass[$row->sumber_data] ?? 'badge-dark') . '">'
                    . strtoupper($row->sumber_data) . '</span>';
            })
            ->rawColumns(['aksi', 'status', 'sumber_data'])
            ->setRowId('id_sertifikasi');
    }

    public function query(PSertifikasiModel $model): QueryBuilder
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $query = $model->newQuery()->with('user.profile');

        if ($user->hasRole('DOS') && $user->id_user) {
            $query->where('id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('sumber_data', $sumber);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $isAdm = $user->hasRole('ADM');
        $isAng = $user->hasRole('ANG');

        $builder = $this->builder()
            ->setTableId('p_sertifikasi-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();

        if ($isAdm) {
            $builder->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
        } elseif ($isAng) {
            $builder->buttons([
                Button::make('reset'),
                Button::make('reload')
            ]);
        } else {
            $builder->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
        }

        return $builder;
    }

    public function getColumns(): array
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $isDos = $user->hasRole('DOS');

        $columns = [
            Column::make('id_sertifikasi')->title('ID'),
            Column::make('tahun_diperoleh')->title('Tahun Diperoleh'),
            Column::make('penerbit')->title('Penerbit'),
            Column::make('nama_sertifikasi')->title('Nama Sertifikasi'),
            Column::make('nomor_sertifikat')->title('Nomor Sertifikat'),
            Column::make('masa_berlaku')->title('Masa Berlaku'),
            Column::make('status')->title('Status')->addClass('text-center'),
            Column::make('sumber_data')->title('Sumber Data')->addClass('text-center'),
            Column::computed('aksi')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];

        if (!$isDos) {
            array_splice($columns, 1, 0, [
                Column::make('nama_lengkap')->title('Nama Dosen')
            ]);
        }

        return $columns;
    }

    protected function filename(): string
    {
        return 'PSertifikasi_' . date('YmdHis');
    }
}
