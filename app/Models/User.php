<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Loggable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_blocked',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_admin' => 'boolean',
            'is_blocked' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function generateVerificationCode(): string
    {
        $this->verification_code = strtoupper(substr(md5(uniqid()), 0, 6));
        $this->verification_code_expires_at = now()->addHours(24);
        $this->save();

        return $this->verification_code;
    }

    public function isValidVerificationCode($code): bool
    {
        return $this->verification_code === $code && 
               $this->verification_code_expires_at->isFuture();
    }
    
    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission($permission)
    {
        if ($this->is_admin) {
            return true; // Administradores têm todas as permissões
        }

        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Atribui permissões ao usuário
     */
    public function assignPermissions($permissions)
    {
        $this->permissions = $permissions;
        $this->save();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}