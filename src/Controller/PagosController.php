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
        $identity = $this->Authentication->getIdentity();
        $userId = (int)$identity->getIdentifier();

        if ($this->isAdminOrEmpleado()) {
            $query = $this->Pagos->find()->orderByDesc('Pagos.fecha_actualizacion');
        } else {
            $query = $this->Pagos->find()
                ->where(['Pagos.user_id' => $userId])
                ->orderByDesc('Pagos.fecha_actualizacion');
        }

        $pagos = $this->paginate($query);
        $this->set(compact('pagos'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $pago = $this->fetchOwnedPago((int)$id);
        $this->set(compact('pago'));
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $pago = $this->Pagos->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (!$this->isAdminOrEmpleado()) {
                $data['user_id'] = $this->Authentication->getIdentity()->getIdentifier();
            }
            $pago = $this->Pagos->patchEntity($pago, $data);
            if ($this->Pagos->save($pago)) {
                $this->Flash->success(__('El pago se guardó correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el pago.'));
        }

        if ($this->isAdminOrEmpleado()) {
            $users = $this->fetchUsersList();
            $this->set(compact('users'));
        }
        $this->set(compact('pago'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $pago = $this->fetchOwnedPago((int)$id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pago = $this->Pagos->patchEntity($pago, $this->request->getData());
            if ($this->Pagos->save($pago)) {
                $this->Flash->success(__('El pago se guardó correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el pago.'));
        }

        if ($this->isAdminOrEmpleado()) {
            $users = $this->fetchUsersList();
            $this->set(compact('users'));
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
        $pago = $this->fetchOwnedPago((int)$id);
        if ($this->Pagos->delete($pago)) {
            $this->Flash->success(__('El pago se eliminó.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el pago.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param int $id Id de pago
     * @return \App\Model\Entity\Pago
     */
    protected function fetchOwnedPago(int $id): \App\Model\Entity\Pago
    {
        $userId = (int)$this->Authentication->getIdentity()->getIdentifier();
        $pago = $this->Pagos->get($id, contain: []);

        if (!$this->isAdminOrEmpleado() && (int)$pago->user_id !== $userId) {
            throw new \Cake\Http\Exception\ForbiddenException(__('No puede acceder a este pago.'));
        }

        return $pago;
    }

    /**
     * @return array<int, string>
     */
    protected function fetchUsersList(): array
    {
        $users = $this->Users->find('list', keyField: 'id', valueField: function ($entity) {
            return $entity->nombre . ' ' . $entity->apellido;
        })->orderBy(['nombre' => 'ASC', 'apellido' => 'ASC']);

        return $users->toArray();
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
