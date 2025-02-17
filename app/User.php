<?php

namespace App;

use App\Models\LegacyEmployee;
use App\Models\LegacyUserType;
use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int            $id
 * @property string         $login
 * @property string         $password
 * @property LegacyUserType $type
 * @property LegacyEmployee $employee
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'pmieducar.usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_usuario';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_usuario;
    }

    /**
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->employee->email;
    }

    /**
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->employee->login;
    }

    /**
     * @return string
     */
    public function getPasswordAttribute()
    {
        return $this->employee->password;
    }

    /**
     * @param string $password
     *
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->employee->password = $password;
        $this->employee->save();
    }

    /**
     * @return string
     */
    public function getRememberTokenAttribute()
    {
        return $this->employee->remember_token;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setRememberTokenAttribute($token)
    {
        $this->employee->remember_token = $token;
        $this->employee->save();
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type->level === LegacyUserType::LEVEL_ADMIN;
    }

    /**
     * @return bool
     */
    public function isSchooling()
    {
        return $this->type->level === LegacyUserType::LEVEL_SCHOOLING;
    }

    /**
     * @return bool
     */
    public function isInstitutional()
    {
        return $this->type->level === LegacyUserType::LEVEL_INSTITUTIONAL;
    }


    /**
     * @return bool
     */
    public function isLibrary()
    {
        return $this->type->level === LegacyUserType::LEVEL_LIBRARY;
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(LegacyEmployee::class, 'cod_usuario', 'ref_cod_pessoa_fj');
    }

    /**
     * @return BelongsToMany
     */
    public function processes()
    {
        return $this->belongsToMany(
            Menu::class,
            'pmieducar.menu_tipo_usuario',
            'ref_cod_tipo_usuario',
            'menu_id',
            'ref_cod_tipo_usuario',
            'id'
        )->withPivot(['visualiza', 'cadastra', 'exclui']);
    }

    /**
     * @return BelongsToMany
     */
    public function menu()
    {
        return $this->processes()
            ->wherePivot('visualiza', 1)
            ->withPivot(['visualiza', 'cadastra', 'exclui']);
    }

    /**
     * @return BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(
            School::class,
            'pmieducar.escola_usuario',
            'ref_cod_usuario',
            'ref_cod_escola',
            'cod_usuario',
            'cod_escola'
        );
    }
}
