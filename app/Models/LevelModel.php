<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'level';
    protected $primaryKey = 'id_level';
    protected $fillable = ['kode_level', 'nama_level'];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return Carbon::instance($date)
            ->timezone(config('app.timezone'))
            ->format('d-m-Y | H:i:s');
    }

    public function users()
    {
        return $this->hasMany(UserModel::class, 'id_level');
    }
}
