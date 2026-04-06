<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\PagosTable;
use Cake\Event\EventInterface;

/**
 * CRUD de pagos.
 *
 * @property \App\Model\Table\PagosTable $Pagos
 */
class PagosController extends AppController
{
    /**
     * @return void
     */
    public function index(): void
    {
        $query = $this->Pagos->find()->orderByDesc('Pagos.fecha_actualizacion');
        $pagos = $this->paginate($query);
        $this->set(compact('pagos'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $pago = $this->Pagos->get($id, contain: []);
        $this->set(compact('pago'));
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $pago = $this->Pagos->newEmptyEntity();
        if ($this->request->is('post')) {
            $pago = $this->Pagos->patchEntity($pago, $this->request->getData());
            if ($this->Pagos->save($pago)) {
                $this->Flash->success(__('El pago se guardó correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el pago.'));
        }
        $this->set(compact('pago'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $pago = $this->Pagos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pago = $this->Pagos->patchEntity($pago, $this->request->getData());
            if ($this->Pagos->save($pago)) {
                $this->Flash->success(__('El pago se guardó correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el pago.'));
        }
        $this->set(compact('pago'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pago = $this->Pagos->get($id);
        if ($this->Pagos->delete($pago)) {
            $this->Flash->success(__('El pago se eliminó.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el pago.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Etiquetas localizadas para enum `metodo`.
     *
     * @return array<string, string>
     */
    protected function labelsMetodo(): array
    {
        $keys = PagosTable::metodos();

        return array_combine($keys, [
            __('Tarjeta de crédito'),
            __('Tarjeta de débito'),
            __('PayPal'),
            __('Transferencia'),
            __('Efectivo'),
            __('Criptomoneda'),
        ]);
    }

    /**
     * Etiquetas localizadas para enum `estado`.
     *
     * @return array<string, string>
     */
    protected function labelsEstado(): array
    {
        $keys = PagosTable::estados();

        return array_combine($keys, [
            __('Pendiente'),
            __('Completado'),
            __('Fallido'),
            __('Reembolsado'),
            __('Cancelado'),
        ]);
    }

    /**
     * @param \Cake\Event\EventInterface $event Evento
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->set('labelsMetodo', $this->labelsMetodo());
        $this->set('labelsEstado', $this->labelsEstado());
    }
}
