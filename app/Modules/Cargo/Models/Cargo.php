<?php

namespace App\Modules\Cargo\Models;

use App\Modules\Cargo\Enums\Stacking;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = null; // No database table
    protected $primaryKey = null; // No primary key
    public $timestamps = false; // Disable timestamps

    public float $length;
    public float $width;
    public float $height;
    public float $weight;
    public float $quantity;
    public $stacking;

    protected $fillable = [
        'length',
        'width',
        'height',
        'weight',
        'quantity',
        'stacking',
    ];

    protected function casts()
    {
        return [
            'length' => 'float',
            'width' => 'float',
            'height' => 'float',
            'weight' => 'float',
            'quantity' => 'float',
            'stacking' => Stacking::class
        ];
    }
}
