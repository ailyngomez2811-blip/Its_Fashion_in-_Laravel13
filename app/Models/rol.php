<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'descripcion',
    ];

    /**
     * Usuarios que pertenecen a este rol.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
