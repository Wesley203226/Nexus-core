<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'document',
        'is_active',
        'notes',
        'profile_photo_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        return asset('storage/'.$this->profile_photo_path);
    }
}
