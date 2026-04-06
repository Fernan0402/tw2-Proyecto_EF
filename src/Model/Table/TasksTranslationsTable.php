<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Tabla shadow de traducciones para `tasks`.
 *
 * @method \App\Model\Entity\TasksTranslation newEmptyEntity()
 */
class TasksTranslationsTable extends Table
{
    /**
     * @param array<string, mixed> $config Configuración
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tasks_translations');
        $this->setDisplayField('title');
        $this->setPrimaryKey(['id', 'locale']);

        $this->belongsTo('Tasks', [
            'foreignKey' => 'id',
            'joinType' => 'INNER',
        ]);
    }
}
