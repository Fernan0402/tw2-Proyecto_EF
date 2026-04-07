<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $correo
 * @property string $password
 * @property string|null $telefono
 * @property string|null $language
 * @property string|null $rol
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class User extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'nombre' => true,
        'apellido' => true,
        'correo' => true,
        'password' => true,
        'telefono' => true,
        'language' => true,
        'rol' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
    ];

    /**
     * @param string $password Contraseña en texto plano
     * @return string
     */
    protected function _setPassword(string $password): string
    {
        return (new DefaultPasswordHasher())->hash($password);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * @return bool
     */
    public function isEmpleado(): bool
    {
        return $this->rol === 'empleado';
    }

    /**
     * @return bool
     */
    public function isUsuario(): bool
    {
        return $this->rol === 'usuario';
    }
}
