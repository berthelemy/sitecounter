<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = \CodeIgniter\Shield\Models\UserModel::factory();

        // Create admin user
        $user = $users->createUser([
            'username' => 'admin',
            'email'    => 'admin@sitecounter.local',
            'password' => 'admin123456',
        ]);

        // Update with additional fields
        $userModel = new \App\Models\UserModel();
        $userModel->update($user->id, [
            'firstname' => 'Site',
            'lastname' => 'Admin',
        ]);

        // Add to admin group
        $user->addGroup('admin');
    }
}
