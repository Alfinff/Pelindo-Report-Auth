<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'uuid',
        'password',
        'role',
        'key',
        'otp',
        'email',
        'no_hp',
        'reset_pswd_count',
        'reset_pswd_at'
    ];

    protected $connection = 'pelindo_repport';
    protected $table      = 'ms_users';
    protected $guarded    = [];

    public function role()
    {
        return $this->hasOne(Role::class, 'kode', 'role');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'uuid');
    }

    public function getCreatedAtAttribute($value)
    {
        return formatTanggal($value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return formatTanggal($value);
    }

    public function getKeyAttribute($value)
    {
        if (!$value) {
            return '';
        }

        return $value;
    }

    public function getOtpAttribute($value)
    {
        if (!$value) {
            return '';
        }

        return $value;
    }
}
