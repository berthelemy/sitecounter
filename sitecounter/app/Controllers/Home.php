<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // If logged in, go to dashboard
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        // If not installed, go to installer
        $installModel = new \App\Models\InstallModel();
        if (!$installModel->isInstalled()) {
            return redirect()->to('/install');
        }

        // Otherwise go to login
        return redirect()->to('/login');
    }
}
