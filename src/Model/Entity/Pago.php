<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Registro de pago.
 *
 * @property int $id
 * @property int $user_id
 * @property string $metodo
 * @property string $monto
 * @property string $estado
 * @property string|null $descripcion
 * @property \Cake\I18n\DateTime|null $fecha_pago
 * @property \Cake\I18n\DateTime $fecha_creacion
 * @property \Cake\I18n\DateTime $fecha_actualizacion
 */
class Pago extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'metodo' => true,
        'monto' => true,
        'estado' => true,
        'descripcion' => true,
        'fecha_pago' => true,
    ];
}
