<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pago $pago
 * @var array $users
 * @var array<string, string> $labelsMetodo
 * @var array<string, string> $labelsEstado
 */
$this->assign('title', __('Registrar pago'));
?>
<div class="pagos form content">
    <?= $this->Form->create($pago) ?>
    <fieldset>
        <legend><?= __('Datos del pago') ?></legend>
        <?php if (isset($users) && !empty($users)): ?>
            <?= $this->Form->control('user_id', ['label' => __('Usuario'), 'options' => $users, 'empty' => __('Seleccionar usuario')]) ?>
        <?php endif; ?>
        <?= $this->Form->control('metodo', ['label' => __('Método'), 'options' => $labelsMetodo]) ?>
        <?= $this->Form->control('monto', ['label' => __('Monto'), 'type' => 'number', 'step' => '0.01', 'min' => 0]) ?>
        <?= $this->Form->control('estado', ['label' => __('Estado'), 'options' => $labelsEstado, 'default' => 'pendiente']) ?>
        <?= $this->Form->control('descripcion', ['label' => __('Descripción'), 'type' => 'textarea']) ?>
        <?= $this->Form->control('fecha_pago', ['label' => __('Fecha de pago'), 'empty' => true]) ?>
    </fieldset>
    <?= $this->Form->button(__('Guardar')) ?>
    <?= $this->Form->end() ?>
    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'button button-outline']) ?>
</div>
