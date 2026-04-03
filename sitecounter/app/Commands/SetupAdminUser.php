<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Shield\Entities\User;

class SetupAdminUser extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group       = 'SiteCounter';
    protected $name        = 'admin:setup';
    protected $description = 'Set up admin user with email and password';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('username', 'admin')->first();

        if (!$user) {
            CLI::error('Admin user not found. Please run migrations first.');
            return EXIT_ERROR;
        }

        CLI::write('Setting up admin user...', 'yellow');

        // Get email
        $email = CLI::prompt('Enter admin email address', 'admin@sitecounter.local', 'required|valid_email');
        if (!$email) {
            CLI::error('Email is required');
            return EXIT_ERROR;
        }

        // Get password
        $password = CLI::prompt('Enter admin password (min 8 characters)', null, 'required|min_length[8]');
        if (!$password) {
            CLI::error('Password is required and must be at least 8 characters');
            return EXIT_ERROR;
        }

        // Get name
        $firstname = CLI::prompt('Enter first name', 'Site');
        $lastname = CLI::prompt('Enter last name', 'Admin');

        try {
            // Update user basic info
            $userModel->update($user->id, [
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]);

            // Update identity with new email and password
            $db = \Config\Database::connect();
            $db->table('auth_identities')
               ->where('user_id', $user->id)
               ->where('type', 'email_password')
               ->update([
                   'secret' => $email,
                   'secret2' => password_hash($password, PASSWORD_DEFAULT),
                   'updated_at' => date('Y-m-d H:i:s')
               ]);

            CLI::write('Admin user setup completed successfully!', 'green');
            CLI::write("Email: {$email}", 'cyan');
            CLI::write("Name: {$firstname} {$lastname}", 'cyan');

        } catch (\Exception $e) {
            CLI::error('Failed to setup admin user: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}