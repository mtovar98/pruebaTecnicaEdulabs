<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileItem extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'stored_path',
        'size_bytes',
        'mime_type',
        'extension',
        'checksum',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
