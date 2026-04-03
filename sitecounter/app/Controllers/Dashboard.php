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
}