<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
    ];

    protected $validationRules = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'firstname' => 'permit_empty|alpha_space|min_length[2]|max_length[50]',
        'lastname' => 'permit_empty|alpha_space|min_length[2]|max_length[50]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken.',
        ],
        'email' => [
            'is_unique' => 'This email address is already registered.',
        ],
        'password' => [
            'min_length' => 'Password must be at least 8 characters long.',
        ],
        'firstname' => [
            'required' => 'First name is required.',
            'alpha_space' => 'First name can only contain letters and spaces.',
        ],
        'lastname' => [
            'required' => 'Last name is required.',
            'alpha_space' => 'Last name can only contain letters and spaces.',
        ],
    ];

    /**
     * Get user by email
     */
    public function findByEmail(string $email): ?object
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get full name
     */
    public function getFullName($user): string
    {
        if (is_object($user)) {
            return trim($user->firstname . ' ' . $user->lastname);
        }

        $user = $this->find($user);
        return $user ? trim($user->firstname . ' ' . $user->lastname) : '';
    }
}