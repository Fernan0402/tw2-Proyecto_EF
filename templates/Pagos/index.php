<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Pago> $pagos
 * @var array<string, string> $labelsMetodo
 * @var array<string, string> $labelsEstado
 */
$this->assign('title', __('Pagos'));
?>
<div class="pagos index content">
    <div class="table-actions" style="margin-bottom: 1.5rem;">
        <?= $this->Html->link(__('Registrar pago'), ['action' => 'add'], ['class' => 'button']) ?>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= __('Id') ?></th>
                    <th><?= __('Método') ?></th>
                    <th><?= __('Monto') ?></th>
                    <th><?= __('Estado') ?></th>
                    <th><?= __('Fecha de pago') ?></th>
                    <th class="actions"><?= __('Acciones') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago) : ?>
                <tr>
                    <td><?= $this->Number->format($pago->id) ?></td>
                    <td><?= h($labelsMetodo[$pago->metodo] ?? $pago->metodo) ?></td>
                    <td><?= $this->Number->format((float)$pago->monto, ['places' => 2, 'precision' => 2]) ?></td>
                    <td><?= h($labelsEstado[$pago->estado] ?? $pago->estado) ?></td>
                    <td><?= $pago->fecha_pago ? h($pago->fecha_pago->i18nFormat()) : '—' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $pago->id]) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $pago->id]) ?>
                        <?= $this->Form->postLink(
                            __('Eliminar'),
                            ['action' => 'delete', $pago->id],
                            ['confirm' => __('¿Eliminar este pago?')]
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
