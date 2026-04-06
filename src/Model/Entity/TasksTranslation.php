<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fila de traducción (locale distinto del por defecto).
 *
 * @property int $id
 * @property string $locale
 * @property string|null $title
 * @property string|null $description
 */
class TasksTranslation extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'id' => true,
        'locale' => true,
        'title' => true,
        'description' => true,
    ];
}
