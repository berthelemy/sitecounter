<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateAdminUser extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group       = 'SiteCounter';
    protected $name        = 'admin:update';
    protected $description = 'Update admin user profile information';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

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
            CLI::error('Admin user not found');
            return EXIT_ERROR;
        }

        $userModel->update($user->id, [
            'firstname' => 'Site',
            'lastname' => 'Admin',
        ]);

        CLI::write('Admin user profile updated successfully', 'green');
        return EXIT_SUCCESS;
    }
}
