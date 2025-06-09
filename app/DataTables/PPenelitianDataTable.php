<?php

namespace App\DataTables;

use App\Models\PPenelitianModel;
use App\Models\ProfileUser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class PPenelitianDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $isDos = $user->hasRole('DOS');
        $isAdm = $user->hasRole('ADM');
        $isAng = $user->hasRole('ANG');

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) use ($user, $isDos, $isAdm) {
                $buttons = [];
                $detailUrl = route('portofolio.penelitian.detail_ajax', $row->id_penelitian);

                $buttons[] = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-sm btn-info" style="margin-left: 5px;">
                    <i class="fas fa-info-circle"></i> Detail
                </button>';

                if ($isDos) {
                    $validasiUrl = route('portofolio.penelitian.validasi_ajax', $row->id_penelitian);
                    $buttons[] = '<button onclick="modalAction(\'' . $validasiUrl . '\')" class="btn btn-sm btn-warning" style="margin-left: 5px;">
                        <i class="fas fa-check-circle"></i> Validasi
                    </button>';
                }

                if ($isDos || $isAdm) {
                    $editUrl = route('portofolio.penelitian.edit_ajax', $row->id_penelitian);
                    $deleteUrl = route('portofolio.penelitian.confirm_ajax', $row->id_penelitian);

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
            ->editColumn('melibatkan_mahasiswa_s2', function ($row) {
                return $row->melibatkan_mahasiswa_s2 ?
                    '<span class="badge p-2 badge-success">YA</span>' :
                    '<span class="badge p-2 badge-secondary">TIDAK</span>';
            })
            ->editColumn('dana', function ($row) {
                return 'Rp ' . number_format($row->dana, 0, ',', '.');
            })
            ->rawColumns(['aksi', 'status', 'sumber_data', 'melibatkan_mahasiswa_s2'])
            ->setRowId('id_penelitian');
    }

    public function query(PPenelitianModel $model): QueryBuilder
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $query = $model->newQuery()
            ->select('p_penelitian.*', 'profile_user.nama_lengkap')
            ->leftJoin('user', 'p_penelitian.id_user', '=', 'user.id_user')
            ->leftJoin('profile_user', 'user.id_user', '=', 'profile_user.id_user');

        if ($user->hasRole('DOS') && $user->id_user) {
            $query->where('id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('sumber_data', $sumber);
        }

        if ($tahun = request('filter_tahun')) {
            $query->where('tahun', $tahun);
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
            ->setTableId('p_penelitian-table')
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
            Column::make('DT_RowIndex')
                ->title('No')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::make('judul_penelitian')->title('Judul Penelitian'),
            Column::make('skema')->title('Skema'),
            Column::make('tahun')->title('Tahun'),
            // Column::make('dana')->title('Dana'),
            // Column::make('peran')->title('Peran'),
            // Column::make('melibatkan_mahasiswa_s2')->title('Melibatkan Mahasiswa S2')->addClass('text-center'),
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
        return 'PPenelitian_' . date('YmdHis');
    }
}
