<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', __('Mi perfil'));
$languageOptions = ['es' => __('Español'), 'en' => __('English')];
?>
<div class="users form content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Datos personales e idioma') ?></legend>
        <?= $this->Form->control('nombre', ['label' => __('Nombre')]) ?>
        <?= $this->Form->control('apellido', ['label' => __('Apellido')]) ?>
        <?= $this->Form->control('correo', ['label' => __('Correo')]) ?>
        <?= $this->Form->control('password', ['label' => __('Contraseña (vacío para no cambiar)')]) ?>
        <?= $this->Form->control('telefono', ['label' => __('Teléfono')]) ?>
        <?= $this->Form->control('language', [
            'label' => __('Idioma de la interfaz'),
            'options' => $languageOptions,
        ]) ?>
    </fieldset>
    <?= $this->Form->button(__('Guardar perfil')) ?>
    <?= $this->Form->end() ?>

    <div class="content" style="margin-top: 2rem;">
        <h4><?= __('Zona de riesgo') ?></h4>
        <?= $this->Form->postLink(
            __('Eliminar mi cuenta'),
            ['action' => 'delete', $user->id],
            ['confirm' => __('¿Seguro que desea eliminar su cuenta? Esta acción no se puede deshacer.'), 'class' => 'button button-outline']
        ) ?>
    </div>
</div>
