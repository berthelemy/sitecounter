<?php

namespace App\Models;

use CodeIgniter\Model;

class InstallModel extends Model
{
    protected $table = 'install';
    protected $primaryKey = 'id';
    protected $allowedFields = ['installed_at', 'version'];

    public function isInstalled(): bool
    {
        // Check if install table exists and has records
        try {
            $result = $this->db->query('SELECT COUNT(*) as count FROM install');
            $row = $result->getRow();
            return $row && $row->count > 0;
        } catch (\Exception $e) {
            // Table doesn't exist yet
            return false;
        }
    }

    public function install(): bool
    {
        try {
            // Run migrations
            $migration = \Config\Services::migrations();
            $migration->latest();

            // Mark as installed
            $this->db->table('install')->insert([
                'installed_at' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Installation failed: ' . $e->getMessage());
            return false;
        }
    }
}