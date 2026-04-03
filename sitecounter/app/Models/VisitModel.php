<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitModel extends Model
{
    protected $table = 'visits';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'website_id', 'visitor_id', 'url', 'title', 'referrer',
        'user_agent', 'screen_resolution', 'ip_address', 'timestamp'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    // Validation
    protected $validationRules = [
        'website_id' => 'required|integer',
        'visitor_id' => 'required|max_length[36]',
        'url' => 'required|valid_url',
        'timestamp' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'website_id' => [
            'required' => 'Website ID is required.',
            'integer' => 'Website ID must be an integer.',
        ],
        'visitor_id' => [
            'required' => 'Visitor ID is required.',
            'max_length' => 'Visitor ID cannot exceed 36 characters.',
        ],
        'url' => [
            'required' => 'URL is required.',
            'valid_url' => 'Please provide a valid URL.',
        ],
        'timestamp' => [
            'required' => 'Timestamp is required.',
            'valid_date' => 'Please provide a valid timestamp.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get unique visitors for a website in a date range
     */
    public function getUniqueVisitors(int $websiteId, string $startDate, string $endDate): int
    {
        return $this->where('website_id', $websiteId)
                   ->where('timestamp >=', $startDate)
                   ->where('timestamp <=', $endDate)
                   ->distinct()
                   ->countAllResults();
    }

    /**
     * Get total visits for a website in a date range
     */
    public function getTotalVisits(int $websiteId, string $startDate, string $endDate): int
    {
        return $this->where('website_id', $websiteId)
                   ->where('timestamp >=', $startDate)
                   ->where('timestamp <=', $endDate)
                   ->countAllResults();
    }

    /**
     * Get top pages by visit count
     */
    public function getTopPages(int $websiteId, int $limit = 10): array
    {
        return $this->select('url, COUNT(*) as visits')
                   ->where('website_id', $websiteId)
                   ->groupBy('url')
                   ->orderBy('visits', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get bottom pages by visit count
     */
    public function getBottomPages(int $websiteId, int $limit = 10): array
    {
        return $this->select('url, COUNT(*) as visits')
                   ->where('website_id', $websiteId)
                   ->groupBy('url')
                   ->orderBy('visits', 'ASC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get daily visit counts for timeline
     */
    public function getDailyVisits(int $websiteId, string $startDate, string $endDate): array
    {
        return $this->select("DATE(timestamp) as date, COUNT(*) as visits")
                   ->where('website_id', $websiteId)
                   ->where('timestamp >=', $startDate)
                   ->where('timestamp <=', $endDate)
                   ->groupBy('DATE(timestamp)')
                   ->orderBy('date', 'ASC')
                   ->findAll();
    }
}