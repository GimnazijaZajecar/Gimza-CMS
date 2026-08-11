<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'first_name', 'last_name', 'title', 'role', 'image_id', 'username', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function image()
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'created_by_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'staff_subject', 'staff_id', 'subject_id');
    }
}