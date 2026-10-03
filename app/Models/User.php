<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Notification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'must_change_password', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }

    /**
     * User belongs to a role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Tasks created by this user.
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Employee record linked to this user.
     */
   public function employee(): HasOne
{
    return $this->hasOne(Employee::class);
}

    public function generatedPayrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'generated_by');
    }

    public function paidPayrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'paid_by');
    }

public function notifications(): HasMany
{
    return $this->hasMany(Notification::class)->latest();
}
}