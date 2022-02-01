<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetTestRecord extends Model
{
    use ModelTrait;

    protected $fillable = [
        'asset_id',
        'job_id',
        'name',
        'test_date',
        'notes',
        'result'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function asset_test_record_readings()
    {
        return $this->hasMany(AssetTestRecordReading::class);
    }
}