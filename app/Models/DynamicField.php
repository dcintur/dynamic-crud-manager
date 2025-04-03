<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicField extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_page_id',
        'name',
        'type',
        'label',
        'is_required',
        'is_unique',
        'is_searchable',
        'is_sortable',
        'is_visible',
        'options',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_searchable' => 'boolean',
        'is_sortable' => 'boolean',
        'is_visible' => 'boolean'
    ];

    public function page()
    {
        return $this->belongsTo(DynamicPage::class, 'dynamic_page_id');
    }
}