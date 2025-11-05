<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageGlobalSetting extends Model
{
    protected $fillable = ['default_quota_mb'];
}
