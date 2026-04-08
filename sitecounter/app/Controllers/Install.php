<?php

namespace App\Controllers;

use App\Models\InstallModel;

class Install extends BaseController
{
    /**
     * Show installer page when application is not yet installed.
     */
    public function index()
    {
        $installModel = new InstallModel();

        // Check if already installed
        if ($installModel->isInstalled()) {
            return redirect()->to('/')->with('error', lang('SiteCounter.messages.already_installed'));
        }

        return view('install');
    }

    /**
     * Execute installation and return JSON response.
     */
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

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }

        // Run installation
        try {
            $result = $installModel->install($payload);
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? lang('SiteCounter.messages.install_completed') : lang('SiteCounter.messages.install_failed')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}