<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of this file must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Entity\User;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\I18n\I18n;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication', [
            'requireIdentity' => true,
        ]);
    }

    /**
     * Aplica el idioma de la identidad (persistido en BD) para toda la petición.
     * Así la interfaz coincide con la preferencia del usuario sin depender solo del navegador.
     *
     * @param \Cake\Event\EventInterface $event Event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $identity = $this->request->getAttribute('identity');
        if ($identity !== null) {
            $lang = $identity->get('language') ?? 'es';
            I18n::setLocale($this->languageToLocale((string)$lang));
        }
    }

    /**
     * Códigos cortos en `users.language` (es/en) → locales ICU usados por CakePHP.
     *
     * @param string $code Código guardado en BD
     * @return string Locale ICU (p. ej. es_ES)
     */
    protected function languageToLocale(string $code): string
    {
        return match ($code) {
            'en' => 'en_US',
            default => 'es_ES',
        };
    }

    /**
     * Actualiza la sesión de autenticación y el locale tras cambiar `language` en perfil.
     *
     * @param \App\Model\Entity\User $user Usuario guardado
     * @return void
     */
    protected function syncIdentityLocale(User $user): void
    {
        $this->Authentication->setIdentity($user);
        I18n::setLocale($this->languageToLocale((string)($user->language ?? 'es')));
    }
}
