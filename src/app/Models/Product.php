<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property string $name
 * @property int    $popularity
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'popularity'
    ];

    public static function create(array $product)
    {
        $self = new self;
        $self->name = $product['name'];
        $self->popularity = $product['popularity'] ?? null;
        $self->save();

        return $self;
    }
}
