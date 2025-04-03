<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'menu_group',
        'order',
        'is_active'
    ];

    public function fields()
    {
        return $this->hasMany(DynamicField::class);
    }

    public function data()
    {
        return $this->hasMany(DynamicData::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}