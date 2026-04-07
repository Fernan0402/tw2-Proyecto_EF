<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\I18n\I18n;

/**
 * CRUD de tareas del usuario autenticado (con textos traducibles).
 *
 * @property \App\Model\Table\TasksTable $Tasks
 */
class TasksController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface $event Event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Tasks->setLocale(I18n::getLocale());
    }

    /**
     * Listado con búsqueda y filtro de estado (etiquetas localizadas en vistas).
     *
     * @return void
     */
    public function index(): void
    {
        $identity = $this->Authentication->getIdentity();
        $userId = (int)$identity->getIdentifier();

        if ($this->isAdminOrEmpleado()) {
            $query = $this->Tasks->find()
                ->orderByDesc('Tasks.modified');
        } else {
            $query = $this->Tasks->find()
                ->where(['Tasks.user_id' => $userId])
                ->orderByDesc('Tasks.modified');
        }

        $q = $this->request->getQuery('q');
        if (is_string($q) && trim($q) !== '') {
            $term = '%' . trim(str_replace(['%', '_'], ['\\%', '\\_'], $q)) . '%';
            $query->where([
                'OR' => [
                    'Tasks.title LIKE' => $term,
                    'Tasks.description LIKE' => $term,
                ],
            ]);
        }

        $status = $this->request->getQuery('status');
        if (is_string($status) && $status !== '' && in_array($status, ['pending', 'in_progress', 'completed'], true)) {
            $query->where(['Tasks.status' => $status]);
        }

        $tasks = $this->paginate($query);
        $this->set(compact('tasks'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $task = $this->fetchOwnedTask((int)$id, []);
        $this->set(compact('task'));
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $task = $this->Tasks->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if ($this->isAdminOrEmpleado() && empty($data['user_id'])) {
                $this->Flash->error(__('Debe seleccionar un usuario.'));
                $users = $this->fetchUsersList();
                $this->set(compact('users', 'task'));
                return null;
            }
            if (!$this->isAdminOrEmpleado()) {
                $data['user_id'] = $this->Authentication->getIdentity()->getIdentifier();
            }
            $task = $this->Tasks->patchEntity($task, $data, [
                'associated' => ['_translations' => true],
            ]);
            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('La tarea se guardó correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la tarea. Revise los datos.'));
        }

        if ($this->isAdminOrEmpleado()) {
            $users = $this->fetchUsersList();
            $this->set(compact('users'));
        }
        $this->set(compact('task'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $task = $this->fetchOwnedTask((int)$id, ['TasksTranslations']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData(), [
                'associated' => ['_translations' => true],
            ]);
            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('La tarea se guardó correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la tarea. Revise los datos.'));
        }

        if ($this->isAdminOrEmpleado()) {
            $users = $this->fetchUsersList();
            $this->set(compact('users'));
        }
        $this->set(compact('task'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $task = $this->fetchOwnedTask((int)$id, []);
        if ($this->Tasks->delete($task)) {
            $this->Flash->success(__('La tarea se eliminó.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar la tarea.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param int $id Id de tarea
     * @param array<string, mixed> $contain Asociaciones para `get()`
     * @return \App\Model\Entity\Task
     */
    protected function fetchOwnedTask(int $id, array $contain): \App\Model\Entity\Task
    {
        $userId = (int)$this->Authentication->getIdentity()->getIdentifier();
        $task = $this->Tasks->get($id, contain: $contain);

        if (!$this->isAdminOrEmpleado() && (int)$task->user_id !== $userId) {
            throw new \Cake\Http\Exception\ForbiddenException(__('No puede acceder a esta tarea.'));
        }

        return $task;
    }

    /**
     * @return array<int, string>
     */
    protected function fetchUsersList(): array
    {
        /** @var \App\Model\Table\UsersTable $usersTable */
        $usersTable = $this->fetchTable('Users');
        $users = $usersTable->find('list', keyField: 'id', valueField: function ($entity) {
            return $entity->nombre . ' ' . $entity->apellido;
        })->orderBy(['nombre' => 'ASC', 'apellido' => 'ASC']);

        return $users->toArray();
    }
}
