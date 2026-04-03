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
            return redirect()->to('/')->with('error', 'SiteCounter is already installed.');
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
                'message' => 'SiteCounter is already installed.'
            ]);
        }

        // Run installation
        try {
            $result = $installModel->install();
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? 'Installation completed successfully!' : 'Installation failed.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Installation error: ' . $e->getMessage()
            ]);
        }
    }
}