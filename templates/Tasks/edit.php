<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
$this->assign('title', __('Editar tarea'));
$statusOptions = [
    'pending' => __('Pendiente'),
    'in_progress' => __('En curso'),
    'completed' => __('Completada'),
];
?>
<div class="tasks form content">
    <?= $this->Form->create($task) ?>
    <fieldset>
        <legend><?= __('Contenido en español (idioma base)') ?></legend>
        <?= $this->Form->control('title', ['label' => __('Título')]) ?>
        <?= $this->Form->control('description', ['label' => __('Descripción'), 'type' => 'textarea']) ?>
        <?= $this->Form->control('status', ['label' => __('Estado'), 'options' => $statusOptions]) ?>
        <?= $this->Form->control('due_date', ['label' => __('Fecha de vencimiento'), 'empty' => true]) ?>
    </fieldset>
    <fieldset>
        <legend><?= __('Traducción al inglés (opcional)') ?></legend>
        <?= $this->Form->control('_translations.en_US.title', ['label' => __('Título (English)')]) ?>
        <?= $this->Form->control('_translations.en_US.description', ['label' => __('Descripción (English)'), 'type' => 'textarea']) ?>
    </fieldset>
    <?= $this->Form->button(__('Guardar')) ?>
    <?= $this->Form->end() ?>
    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'button button-outline']) ?>
</div>
