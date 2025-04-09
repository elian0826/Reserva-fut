<?php

namespace App\Modules\RolesPermisos\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'description'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $with = ['permissions'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->guard_name = 'api';
    }
} 