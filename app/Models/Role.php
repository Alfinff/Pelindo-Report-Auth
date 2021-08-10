<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $hidden = [
        'id'
    ];

    protected $connection = 'pelindo_repport';
    protected $table      = 'ms_roles';
    protected $guarded    = [];
}
