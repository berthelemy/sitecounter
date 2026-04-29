<?php

namespace App\Controllers\Auth;

use CodeIgniter\HTTP\RedirectResponse;

class LoginController extends \CodeIgniter\Shield\Controllers\LoginController
{
    /**
     * Attempts to log the user in.
     *
     * Guard against duplicate login attempts if session user data already exists.
     */
    public function loginAction(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $authConfig = config('Auth');
        $sessionField = 'user';

        if (isset($authConfig->sessionConfig['field']) && is_string($authConfig->sessionConfig['field'])) {
            $sessionField = $authConfig->sessionConfig['field'];
        }

        $session = session();
        $sessionUserInfo = $session->get($sessionField);

        // If a login action is already pending, continue that flow instead.
        if (is_array($sessionUserInfo) && ! empty($sessionUserInfo['auth_action'])) {
            return redirect()->route('auth-action-show');
        }

        // Stale user info can trigger Shield LogicException on startLogin().
        if ($session->has($sessionField)) {
            $session->remove($sessionField);
            $session->remove('authenticator');

            log_message('notice', 'Cleared stale auth session data before login attempt.');
        }

        return parent::loginAction();
    }
}
