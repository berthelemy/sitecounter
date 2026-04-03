<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $allowedFields = [
        'username',
        'firstname',
        'lastname',
    ];

    protected $validationRules = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'firstname' => 'permit_empty|alpha_space|min_length[2]|max_length[50]',
        'lastname' => 'permit_empty|alpha_space|min_length[2]|max_length[50]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken.',
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
     * Get user by email identity.
     */
    public function findByEmail(string $email): ?object
    {
        return $this->select('users.*')
            ->join('auth_identities', 'auth_identities.user_id = users.id', 'inner')
            ->where('auth_identities.type', 'email_password')
            ->where('auth_identities.secret', $email)
            ->first();
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