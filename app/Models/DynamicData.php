<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicData extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_page_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function page()
    {
        return $this->belongsTo(DynamicPage::class, 'dynamic_page_id');
    }
}