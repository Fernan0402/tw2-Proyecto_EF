<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Acciones') ?></h4>
            <?= $this->Html->link(__('Lista de Usuarios'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="users form content">
            <?= $this->Form->create($user) ?>
            <fieldset>
                <legend><?= __('Nuevo Usuario') ?></legend>
                <?php
                    echo $this->Form->control('nombre');
                    echo $this->Form->control('apellido');
                    echo $this->Form->control('correo');
                    echo $this->Form->control('password');
                    echo $this->Form->control('telefono', ['label' => __('Teléfono')]);
                    echo $this->Form->control('language', ['label' => __('Idioma'), 'options' => ['es' => 'Español', 'en' => 'English']]);
                    if ($this->isAdmin()) {
                        echo $this->Form->control('rol', ['label' => __('Rol'), 'options' => ['empleado' => 'Empleado', 'usuario' => 'Usuario']]);
                    } else {
                        echo $this->Form->hidden('rol', ['value' => 'usuario']);
                    }
                ?>
            </fieldset>
            <?= $this->Form->button(__('Guardar')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
