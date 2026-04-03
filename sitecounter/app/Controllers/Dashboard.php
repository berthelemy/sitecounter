<?php

namespace App\Controllers;

use CodeIgniter\Shield\Entities\User;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $userModel = new \App\Models\UserModel();

        return view('dashboard/index', [
            'user' => $user,
            'fullName' => $userModel->getFullName($user),
        ]);
    }

    public function profile()
    {
        // Check if user is logged in
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $userModel = new \App\Models\UserModel();

        return view('dashboard/profile', [
            'user' => $user,
            'fullName' => $userModel->getFullName($user),
        ]);
    }

    public function updateProfile()
    {
        // Check if user is logged in
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $userModel = new \App\Models\UserModel();
        $db = \Config\Database::connect();

        $rules = [
            'firstname' => 'required|alpha_space|min_length[2]|max_length[50]',
            'lastname' => 'required|alpha_space|min_length[2]|max_length[50]',
            'email' => 'required|valid_email',
        ];

        $messages = [
            'firstname' => [
                'required'    => lang('SiteCounter.messages.firstname_required'),
                'alpha_space' => lang('SiteCounter.messages.firstname_alpha_space'),
                'min_length'  => lang('SiteCounter.messages.firstname_min_length'),
                'max_length'  => lang('SiteCounter.messages.firstname_max_length'),
            ],
            'lastname' => [
                'required'    => lang('SiteCounter.messages.lastname_required'),
                'alpha_space' => lang('SiteCounter.messages.lastname_alpha_space'),
                'min_length'  => lang('SiteCounter.messages.lastname_min_length'),
                'max_length'  => lang('SiteCounter.messages.lastname_max_length'),
            ],
            'email' => [
                'required'    => lang('SiteCounter.messages.email_required'),
                'valid_email' => lang('SiteCounter.messages.email_valid'),
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = (string) $this->request->getPost('email');
        $emailInUse = $db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->where('user_id !=', $user->id)
            ->countAllResults() > 0;

        if ($emailInUse) {
            return redirect()->back()->withInput()->with('errors', [
                'email' => lang('SiteCounter.messages.email_already_registered'),
            ]);
        }

        $userData = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
        ];

        $db->transStart();
        $userUpdated = $userModel->update($user->id, $userData);
        $identityUpdated = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->update([
                'secret' => $email,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        $db->transComplete();

        if ($userUpdated && $identityUpdated && $db->transStatus()) {
            return redirect()->to('/dashboard/profile')->with('success', lang('SiteCounter.messages.profile_updated'));
        }

        return redirect()->back()->withInput()->with('error', lang('SiteCounter.messages.profile_update_failed'));
    }

    public function changePassword()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required|min_length[8]',
            'new_password_confirm'  => 'required|matches[new_password]',
        ];

        $messages = [
            'current_password' => [
                'required' => lang('SiteCounter.messages.current_password_required'),
            ],
            'new_password' => [
                'required' => lang('SiteCounter.messages.new_password_required'),
                'min_length' => lang('SiteCounter.messages.new_password_min_length'),
            ],
            'new_password_confirm' => [
                'required' => lang('SiteCounter.messages.new_password_confirm_required'),
                'matches' => lang('SiteCounter.messages.new_password_confirm_matches'),
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->to('/dashboard/profile')
                ->withInput()
                ->with('password_errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');

        $user = auth()->user();
        $validPassword = service('passwords')->verify($currentPassword, $user->password_hash);

        if (!$validPassword) {
            return redirect()->to('/dashboard/profile')
                ->with('password_errors', ['current_password' => lang('SiteCounter.messages.current_password_incorrect')]);
        }

        $users = auth()->getProvider();
        $user->fill(['password' => $newPassword]);
        $users->save($user);

        return redirect()->to('/dashboard/profile')->with('password_success', lang('SiteCounter.messages.password_changed'));
    }

    public function updateLanguage()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $locale = $this->request->getPost('language');
        $supported = config('App')->supportedLocales ?? ['en'];

        if (!in_array($locale, $supported, true)) {
            return redirect()->to('/dashboard/profile')->with('error', lang('SiteCounter.messages.unsupported_language'));
        }

        // Persist locale in a cookie for one year.
        $cookie = new \CodeIgniter\Cookie\Cookie(
            'sitecounter_locale',
            $locale,
            ['expires' => time() + YEAR, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']
        );
        $this->response->setCookie($cookie);

        service('request')->setLocale($locale);

        return redirect()->to('/dashboard/profile')->with('success', lang('SiteCounter.messages.language_saved'));
    }
}