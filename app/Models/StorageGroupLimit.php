<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageGroupLimit extends Model
{
    protected $fillable = ['group_id', 'quota_mb'];

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }
}
