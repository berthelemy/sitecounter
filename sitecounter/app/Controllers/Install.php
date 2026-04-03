<?php

namespace App\Controllers;

use App\Models\InstallModel;

class Install extends BaseController
{
    public function index()
    {
        $installModel = new InstallModel();

        // Check if already installed
        if ($installModel->isInstalled()) {
            return redirect()->to('/')->with('error', lang('SiteCounter.messages.already_installed'));
        }

        return view('install');
    }

    public function run()
    {
        $installModel = new InstallModel();

        // Check if already installed
        if ($installModel->isInstalled()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('SiteCounter.messages.already_installed')
            ]);
        }

        // Run installation
        try {
            $result = $installModel->install();
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? lang('SiteCounter.messages.install_completed') : lang('SiteCounter.messages.install_failed')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('SiteCounter.messages.install_error', [$e->getMessage()])
            ]);
        }
    }
}