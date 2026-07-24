<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; //se importa el modelo

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string> //atributos de la tabla que se pueden modificar
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'username',
        'telefono',
        'email',
        'password',
        'rol_id',
        'estado',
        'fecha_registro',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string> //atributos que se ocultan
     */
    protected $hidden = [
        'password', //se oculta la contraseña
        'remember_token', //se oculta el token
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> //atributos que se transforman
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', //se transforma a datetime
            'fecha_registro' => 'datetime', //se transforma a datetime
            'password' => 'hashed', //se transforma a hashed
        ];
    }

    /**
     * Relación con el rol del usuario.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class); //se especifica el id de la tabla usuarios para que sepa que campo comparar
    }
}
