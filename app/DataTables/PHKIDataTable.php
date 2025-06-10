<?php

namespace App\DataTables;

use App\Models\PHKI;
use App\Models\PHKIModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class PHKIDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\UserModel|null $user */
        $user = Auth::user();
        $isDos = $user->hasRole('DOS');
        $isAdm = $user->hasRole('ADM');
        $isAng = $user->hasRole('ANG');

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) use ($user, $isDos, $isAdm) {
                $buttons = [];
                $detailUrl = route('portofolio.hki.detail_ajax', $row->id_hki);

                $buttons[] = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-sm btn-info" style="margin-left: 5px;">
                        <i class="fas fa-info-circle"></i> Detail
                    </button>';

                if ($isDos) {
                    $validasiUrl = route('portofolio.hki.validasi_ajax', $row->id_hki);
                    $buttons[] = '<button onclick="modalAction(\'' . $validasiUrl . '\')" class="btn btn-sm btn-warning" style="margin-left: 5px;">
                            <i class="fas fa-check-circle"></i> Validasi
                        </button>';
                }

                if ($isDos || $isAdm) {
                    $editUrl = route('portofolio.hki.edit_ajax', $row->id_hki);
                    $deleteUrl = route('portofolio.hki.confirm_ajax', $row->id_hki);

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
                return $isDos ? '-' : ($row->nama_lengkap ?? '-');
            })
            ->filterColumn('nama_lengkap', function ($query, $keyword) {
                $query->where('profile_user.nama_lengkap', 'like', "%{$keyword}%");
            })
            ->orderColumn('nama_lengkap', function ($query, $order) {
                $query->orderBy('profile_user.nama_lengkap', $order);
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
            ->setRowId('id_hki');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PHKIModel $model): QueryBuilder
    {
        /** @var \App\Models\UserModel|null $user */
        $user = Auth::user();

        $query = $model->newQuery()
            ->select('p_hki.*', 'profile_user.nama_lengkap')
            ->leftJoin('user', 'p_hki.id_user', '=', 'user.id_user')
            ->leftJoin('profile_user', 'user.id_user', '=', 'profile_user.id_user');

        if ($user->hasRole('DOS') && $user->id_user) {
            $query->where('p_hki.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('sumber_data', $sumber);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        /** @var \App\Models\UserModel|null $user */
        $user = Auth::user();
        $isAdm = $user->hasRole('ADM');
        $isAng = $user->hasRole('ANG');

        $builder = $this->builder()
            ->setTableId('p_hki-table')
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

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        /** @var \App\Models\UserModel|null $user */
        $user = Auth::user();
        $isDos = $user->hasRole('DOS');

        $columns = [
            Column::make('DT_RowIndex')
                ->title('No')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('judul')->title('Judul'),
            Column::make('tahun')->title('Tahun'),
            Column::make('skema')->title('Skema'),
            Column::make('nomor')->title('Nomor HKI'),
            // Column::make('melibatkan_mahasiswa_s2')->title('Melibatkan Mahasiswa S2'),
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
                Column::make('nama_lengkap')->title('Nama Dosen')->name('profile_user.nama_lengkap')->orderable(true)->searchable(true)
            ]);
        }

        return $columns;
    }

    protected function filename(): string
    {
        return 'PPHKI_' . date('YmdHis');
    }
}
