<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Identidad: registro, sesión y perfil (idioma persistente).
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface $event Event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login', 'logout', 'register']);
    }

    /**
     * Acceso al sistema.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function login()
    {
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $redirect = $this->Authentication->getLoginRedirect() ?? ['controller' => 'Tasks', 'action' => 'index'];

            return $this->redirect($redirect);
        }
        if ($this->request->is('post')) {
            $this->Flash->error(__('Correo o contraseña incorrectos.'));
        }
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        $this->Authentication->logout();

        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    /**
     * Registro público (enfoque inclusivo: cuenta propia sin depender de un administrador).
     *
     * @return \Cake\Http\Response|null|void
     */
    public function register()
    {
        if ($this->Authentication->getIdentity()) {
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($user->language === null || $user->language === '') {
                $user->language = 'es';
            }
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Cuenta creada. Inicie sesión con su correo.'));

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error(__('No se pudo registrar. Revise los datos.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Tras el login, la app lleva al tablero de tareas (no a un listado global de personas).
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
    }

    /**
     * Perfil del usuario conectado (edición de datos e idioma de interfaz).
     *
     * @return \Cake\Http\Response|null|void
     */
    public function profile()
    {
        $id = (int)$this->Authentication->getIdentity()->getIdentifier();
        $user = $this->Users->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (isset($data['password']) && $data['password'] === '') {
                unset($data['password']);
            }
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->syncIdentityLocale($user);
                $this->Flash->success(__('Perfil actualizado.'));

                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('No se pudo guardar el perfil.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Cambio rápido de idioma (persiste en BD y actualiza la sesión).
     *
     * @return \Cake\Http\Response|null
     */
    public function changeLanguage()
    {
        $this->request->allowMethod(['post']);
        $lang = $this->request->getData('language');
        if (!is_string($lang) || !in_array($lang, ['es', 'en'], true)) {
            $this->Flash->error(__('Idioma no válido.'));

            return $this->redirect($this->referer(['controller' => 'Tasks', 'action' => 'index'], true));
        }

        $id = (int)$this->Authentication->getIdentity()->getIdentifier();
        $user = $this->Users->get($id);
        $user->language = $lang;
        if ($this->Users->save($user)) {
            $this->syncIdentityLocale($user);
            $this->Flash->success(__('Idioma actualizado.'));
        } else {
            $this->Flash->error(__('No se pudo guardar el idioma.'));
        }

        return $this->redirect($this->referer(['controller' => 'Tasks', 'action' => 'index'], true));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $this->denyUnlessSelf((int)$id);
        $user = $this->Users->get($id, contain: []);
        $this->set(compact('user'));
    }

    /**
     * Alta de usuarios solo con sesión: se desvía al registro público por claridad ética.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $this->Flash->warning(__('Para nuevas cuentas use el registro público.'));

        return $this->redirect(['action' => 'register']);
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $this->denyUnlessSelf((int)$id);
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (isset($data['password']) && $data['password'] === '') {
                unset($data['password']);
            }
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->syncIdentityLocale($user);
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * @param string|null $id Id
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->denyUnlessSelf((int)$id);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Authentication->logout();
            $this->Flash->success(__('Su cuenta fue eliminada.'));

            return $this->redirect(['action' => 'login']);
        }
        $this->Flash->error(__('The user could not be deleted. Please, try again.'));

        return $this->redirect(['action' => 'profile']);
    }

    /**
     * @param int $userId Id de usuario solicitado
     * @return void
     */
    protected function denyUnlessSelf(int $userId): void
    {
        $me = (int)$this->Authentication->getIdentity()->getIdentifier();
        if ($userId !== $me) {
            throw new \Cake\Http\Exception\ForbiddenException(__('No puede gestionar el perfil de otra persona.'));
        }
    }
}
