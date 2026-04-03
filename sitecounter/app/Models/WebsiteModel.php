<?php

namespace App\Models;

use CodeIgniter\Model;

class WebsiteModel extends Model
{
    protected $table = 'websites';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'url', 'token', 'user_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[1]|max_length[255]',
        'url' => 'required|valid_url|max_length[255]',
        'token' => 'required|is_unique[websites.token]|min_length[32]|max_length[64]',
        'user_id' => 'required|integer',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Website name is required.',
            'min_length' => 'Website name must be at least 1 character.',
            'max_length' => 'Website name cannot exceed 255 characters.',
        ],
        'url' => [
            'required' => 'Website URL is required.',
            'valid_url' => 'Please provide a valid URL.',
            'max_length' => 'URL cannot exceed 255 characters.',
        ],
        'token' => [
            'required' => 'Token is required.',
            'is_unique' => 'Token must be unique.',
            'min_length' => 'Token must be at least 32 characters.',
            'max_length' => 'Token cannot exceed 64 characters.',
        ],
        'user_id' => [
            'required' => 'User ID is required.',
            'integer' => 'User ID must be an integer.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Generate a unique token for the website
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32)); // 64 characters
    }

    /**
     * Get websites for a specific user
     */
    public function getUserWebsites(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }
}