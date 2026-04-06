<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Task> $tasks
 */
$this->assign('title', __('Mis tareas'));
$statusFilter = $this->request->getQuery('status', '');
$statusOptions = [
    '' => __('Todos los estados'),
    'pending' => __('Pendiente'),
    'in_progress' => __('En curso'),
    'completed' => __('Completada'),
];
?>
<div class="tasks index content">
    <div class="table-actions" style="margin-bottom: 1.5rem;">
        <?= $this->Html->link(__('Nueva tarea'), ['action' => 'add'], ['class' => 'button']) ?>
    </div>

    <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'index']]) ?>
    <fieldset style="margin-bottom: 1.5rem;">
        <legend><?= __('Búsqueda y filtros') ?></legend>
        <?= $this->Form->control('q', [
            'label' => __('Buscar en título o descripción'),
            'value' => $this->request->getQuery('q'),
            'required' => false,
        ]) ?>
        <?= $this->Form->control('status', [
            'label' => __('Estado'),
            'options' => $statusOptions,
            'value' => $statusFilter,
            'empty' => false,
        ]) ?>
        <?= $this->Form->button(__('Aplicar')) ?>
    </fieldset>
    <?= $this->Form->end() ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= __('Título') ?></th>
                    <th><?= __('Estado') ?></th>
                    <th><?= __('Vence') ?></th>
                    <th class="actions"><?= __('Acciones') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task) : ?>
                <tr>
                    <td><?= h($task->title) ?></td>
                    <td><?= h($statusOptions[$task->status] ?? $task->status) ?></td>
                    <td><?= $task->due_date ? h($task->due_date->i18nFormat()) : '—' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $task->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $task->id]) ?>
                        <?= $this->Form->postLink(
                            __('Eliminar'),
                            ['action' => 'delete', $task->id],
                            ['confirm' => __('¿Eliminar esta tarea?')]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primera')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Última') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}}')) ?></p>
    </div>
</div>
