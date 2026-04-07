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
            if ($user->rol === null || $user->rol === '') {
                $user->rol = 'usuario';
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
     * Lista de usuarios - solo admin
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        if (!$this->isAdmin()) {
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $users = $this->paginate($this->Users->find()->orderBy(['Users.apellido' => 'ASC', 'Users.nombre' => 'ASC']));
        $this->set(compact('users'));
    }

    /**
     * Ver perfil de usuario - admin puede ver cualquiera, otros solo propio
     *
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        if (!$this->isAdmin()) {
            $id = (string)$this->getCurrentUserId();
        }
        $user = $this->Users->get($id, contain: []);
        $this->set(compact('user'));
    }

    /**
     * Alta de usuarios - solo admin y empleado pueden crear
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        if (!$this->isAdminOrEmpleado()) {
            $this->Flash->error(__('No tiene permisos para crear usuarios.'));
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if (!isset($user->rol) || $user->rol === '') {
                $user->rol = 'usuario';
            }
            if ($this->Users->save($user)) {
                $this->Flash->success(__('El usuario fue guardado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el usuario.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Editar usuario - admin puede editar cualquiera, otros solo propio
     *
     * @param string|null $id Id
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        if (!$this->isAdmin()) {
            $id = (string)$this->getCurrentUserId();
        }
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (isset($data['password']) && $data['password'] === '') {
                unset($data['password']);
            }
            if (!$this->isAdmin() && isset($data['rol'])) {
                unset($data['rol']);
            }
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->syncIdentityLocale($user);
                $this->Flash->success(__('El usuario fue guardado.'));

                if ($this->isAdmin()) {
                    return $this->redirect(['action' => 'index']);
                }

                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('No se pudo guardar el usuario.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Eliminar usuario - solo admin
     *
     * @param string|null $id Id
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        if (!$this->isAdmin()) {
            $this->Flash->error(__('No tiene permisos para eliminar usuarios.'));
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('El usuario fue eliminado.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el usuario.'));
        }

        return $this->redirect(['action' => 'index']);
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
            if (!$this->isAdmin() && isset($data['rol'])) {
                unset($data['rol']);
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
}
