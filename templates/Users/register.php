<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', __('Registro'));
$languageOptions = ['es' => __('Español'), 'en' => __('English')];
?>
<div class="users form content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Cree su cuenta') ?></legend>
        <?= $this->Form->control('nombre', ['label' => __('Nombre')]) ?>
        <?= $this->Form->control('apellido', ['label' => __('Apellido')]) ?>
        <?= $this->Form->control('correo', ['label' => __('Correo')]) ?>
        <?= $this->Form->control('password', ['label' => __('Contraseña')]) ?>
        <?= $this->Form->control('telefono', ['label' => __('Teléfono')]) ?>
        <?= $this->Form->control('language', [
            'label' => __('Idioma de la interfaz'),
            'options' => $languageOptions,
            'default' => 'es',
        ]) ?>
    </fieldset>
    <?= $this->Form->button(__('Registrarse')) ?>
    <?= $this->Form->end() ?>
    <p><?= $this->Html->link(__('¿Ya tiene cuenta? Inicie sesión'), ['action' => 'login']) ?></p>
</div>
