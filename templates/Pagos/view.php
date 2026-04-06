<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pago $pago
 * @var array<string, string> $labelsMetodo
 * @var array<string, string> $labelsEstado
 */
$this->assign('title', __('Pago #{0}', $pago->id));
?>
<div class="pagos view content">
    <div class="table-actions">
        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $pago->id], ['class' => 'button']) ?>
        <?= $this->Html->link(__('Volver al listado'), ['action' => 'index'], ['class' => 'button button-outline']) ?>
    </div>
    <table>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($pago->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Método') ?></th>
            <td><?= h($labelsMetodo[$pago->metodo] ?? $pago->metodo) ?></td>
        </tr>
        <tr>
            <th><?= __('Monto') ?></th>
            <td><?= $this->Number->format((float)$pago->monto, ['places' => 2, 'precision' => 2]) ?></td>
        </tr>
        <tr>
            <th><?= __('Estado') ?></th>
            <td><?= h($labelsEstado[$pago->estado] ?? $pago->estado) ?></td>
        </tr>
        <tr>
            <th><?= __('Descripción') ?></th>
            <td><?= $pago->descripcion !== null && $pago->descripcion !== '' ? nl2br(h($pago->descripcion)) : '—' ?></td>
        </tr>
        <tr>
            <th><?= __('Fecha de pago') ?></th>
            <td><?= $pago->fecha_pago ? h($pago->fecha_pago->i18nFormat()) : '—' ?></td>
        </tr>
        <tr>
            <th><?= __('Creado') ?></th>
            <td><?= h($pago->fecha_creacion->i18nFormat()) ?></td>
        </tr>
        <tr>
            <th><?= __('Actualizado') ?></th>
            <td><?= h($pago->fecha_actualizacion->i18nFormat()) ?></td>
        </tr>
    </table>
</div>
