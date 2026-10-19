<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use App\Support\DepartmentPermission;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    use RecordsActivity;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'department_id', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->role_id) {
                $roleCode = $user->role ? $user->role->code : Role::whereKey($user->role_id)->value('code');
                if ($roleCode && DepartmentPermission::isDepartmentRole($roleCode) && $roleCode !== 'department_admin') {
                    $deptId = Department::where('code', $roleCode)->value('id');
                    if ($deptId) {
                        $user->department_id = $deptId;
                    }
                }
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->role !== null;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_active && $this->role?->code === 'super_admin';
    }

    public function isContentAdmin(): bool
    {
        return $this->is_active && $this->role?->code === 'content_admin';
    }

    public function isDepartmentAdmin(): bool
    {
        return $this->is_active && $this->role !== null && DepartmentPermission::isDepartmentRole($this->role->code);
    }

    public function canAccessDepartmentModel(string $modelClass): bool
    {
        return DepartmentPermission::canAccessModel($this, $modelClass);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }
}
