<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size'
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
