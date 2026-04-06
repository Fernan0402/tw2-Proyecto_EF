<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', __('Iniciar sesión'));
?>
<div class="users form content">
    <?php
    // Conserva ?redirect=/ruta para volver ahí tras el POST (p. ej. /users o /users/add).
    $query = $this->request->getQueryParams();
    $formUrl = ['controller' => 'Users', 'action' => 'login'];
    if ($query !== []) {
        $formUrl['?'] = $query;
    }
    ?>
    <?= $this->Form->create(null, ['url' => $formUrl]) ?>
    <fieldset>
        <legend><?= __('Ingrese su correo y contraseña') ?></legend>
        <?= $this->Form->control('correo', ['label' => __('Correo'), 'required' => true, 'autocomplete' => 'username']) ?>
        <?= $this->Form->control('password', ['label' => __('Contraseña'), 'required' => true, 'autocomplete' => 'current-password']) ?>
    </fieldset>
    <?= $this->Form->button(__('Entrar')) ?>
    <?= $this->Form->end() ?>
    <p><?= $this->Html->link(__('Crear una cuenta'), ['action' => 'register']) ?></p>
</div>
