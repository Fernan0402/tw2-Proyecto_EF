<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
$this->assign('title', $task->title);
$statusLabels = [
    'pending' => __('Pendiente'),
    'in_progress' => __('En curso'),
    'completed' => __('Completada'),
];
?>
<div class="tasks view content">
    <div class="table-actions">
        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $task->id], ['class' => 'button']) ?>
        <?= $this->Html->link(__('Volver al listado'), ['action' => 'index'], ['class' => 'button button-outline']) ?>
    </div>
    <h3><?= h($task->title) ?></h3>
    <p><strong><?= __('Estado') ?>:</strong> <?= h($statusLabels[$task->status] ?? $task->status) ?></p>
    <p><strong><?= __('Vence') ?>:</strong> <?= $task->due_date ? h($task->due_date->i18nFormat()) : '—' ?></p>
    <div class="task-body">
        <h4><?= __('Descripción') ?></h4>
        <?php if ($task->description !== null && $task->description !== '') : ?>
            <div><?= nl2br(h($task->description)) ?></div>
        <?php else : ?>
            <p><em><?= __('Sin descripción') ?></em></p>
        <?php endif; ?>
    </div>
</div>
