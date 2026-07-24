<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; //se importa el modelo

class Rol extends Model //se define el modelo 
{
    protected $table = 'roles'; //nombre de la tabla en la bd

    //atributos de la tabla  que se pueden modificar
    protected $fillable = [
        'descripcion',
    ];

    //funcion para relacionar con la tabla usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id'); //se especifica el id de la tabla usuarios para que sepa que campo comparar
    }
}
