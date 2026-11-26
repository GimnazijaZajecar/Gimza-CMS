<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\DeletesStoredFiles;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'first_name', 'last_name', 'title', 'role', 'username', 'phone', 'photo', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use DeletesStoredFiles;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /** @var array<string> */
    protected array $storedFiles = ['photo'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Колона name се попуњава сама од имена и презимена.
        static::saving(function (User $user): void {
            $fullName = trim("{$user->first_name} {$user->last_name}");

            if ($fullName !== '') {
                $user->name = $fullName;
            } elseif (blank($user->name)) {
                $user->name = $user->username ?: $user->email;
            }
        });
    }

    /**
     * У панел улазе само активни корисници.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->photo ? Storage::disk('public')->url($this->photo) : null;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'staff_subject', 'staff_id', 'subject_id')->withTimestamps();
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
