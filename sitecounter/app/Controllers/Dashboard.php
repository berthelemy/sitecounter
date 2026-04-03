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

        $rules = [
            'firstname' => 'required|alpha_space|min_length[2]|max_length[50]',
            'lastname' => 'required|alpha_space|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $user->id . ']',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'email' => $this->request->getPost('email'),
        ];

        if ($userModel->update($user->id, $data)) {
            return redirect()->to('/dashboard/profile')->with('success', 'Profile updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update profile');
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

        if (!$this->validate($rules)) {
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
                ->with('password_errors', ['current_password' => 'Current password is incorrect.']);
        }

        $users = auth()->getProvider();
        $user->fill(['password' => $newPassword]);
        $users->save($user);

        return redirect()->to('/dashboard/profile')->with('password_success', 'Password changed successfully.');
    }

    public function updateLanguage()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $locale = $this->request->getPost('language');
        $supported = config('App')->supportedLocales ?? ['en'];

        if (!in_array($locale, $supported, true)) {
            return redirect()->to('/dashboard/profile')->with('error', 'Unsupported language.');
        }

        // Persist locale in a cookie for one year.
        $cookie = new \CodeIgniter\Cookie\Cookie(
            'sitecounter_locale',
            $locale,
            ['expires' => time() + YEAR, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']
        );
        $this->response->setCookie($cookie);

        service('request')->setLocale($locale);

        return redirect()->to('/dashboard/profile')->with('success', 'Language preference saved.');
    }
}