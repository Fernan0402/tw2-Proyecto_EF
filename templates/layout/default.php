<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'App EF';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="top-nav">
        <div class="top-nav-title">
            <a href="<?= $this->Url->build('/') ?>"><span>App</span> EF</a>
        </div>
        <div class="top-nav-links">
            <?php if ($this->Identity->isLoggedIn()) : ?>
                <?php if ($this->isAdmin()) : ?>
                    <?= $this->Html->link(__('Usuarios'), ['controller' => 'Users', 'action' => 'index']) ?>
                    <?= $this->Html->link(__('Tareas'), ['controller' => 'Tasks', 'action' => 'index']) ?>
                    <?= $this->Html->link(__('Pagos'), ['controller' => 'Pagos', 'action' => 'index']) ?>
                <?php elseif ($this->isAdminOrEmpleado()) : ?>
                    <?= $this->Html->link(__('Tareas'), ['controller' => 'Tasks', 'action' => 'index']) ?>
                    <?= $this->Html->link(__('Pagos'), ['controller' => 'Pagos', 'action' => 'index']) ?>
                <?php else : ?>
                    <?= $this->Html->link(__('Mis Tareas'), ['controller' => 'Tasks', 'action' => 'index']) ?>
                    <?= $this->Html->link(__('Mis Pagos'), ['controller' => 'Pagos', 'action' => 'index']) ?>
                <?php endif; ?>
                <?= $this->Html->link(__('Mi perfil'), ['controller' => 'Users', 'action' => 'profile']) ?>
                <?= $this->Form->postLink(__('Español'), ['controller' => 'Users', 'action' => 'changeLanguage'], ['data' => ['language' => 'es'], 'class' => '']) ?>
                <?= $this->Form->postLink(__('English'), ['controller' => 'Users', 'action' => 'changeLanguage'], ['data' => ['language' => 'en'], 'class' => '']) ?>
                <?= $this->Html->link(__('Salir'), ['controller' => 'Users', 'action' => 'logout']) ?>
            <?php else : ?>
                <?= $this->Html->link(__('Iniciar sesión'), ['controller' => 'Users', 'action' => 'login']) ?>
                <?= $this->Html->link(__('Registro'), ['controller' => 'Users', 'action' => 'register']) ?>
            <?php endif; ?>
        </div>
    </nav>
    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer>
    </footer>
</body>
</html>
