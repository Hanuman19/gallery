<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $title
 * @property ?string $description
 * @property string $path
 */
class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';
    protected $fillable = [
        'title',
        'description',
        'path',
    ];
}
