<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

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
    protected $updatedField = '';

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
     * Cached allowed URL prefixes per website.
     *
     * @var array<int, list<string>>
     */
    private array $allowedUrlPrefixes = [];

    /**
     * Get unique visitors for a website in a date range
     */
    public function getUniqueVisitors(int $websiteId, string $startDate, string $endDate): int
    {
        $builder = $this->db->table('visits')
            ->select('COUNT(DISTINCT visitor_id) as count', false)
            ->where('website_id', $websiteId)
            ->where('timestamp >=', $startDate)
            ->where('timestamp <=', $endDate);

        $this->applyTrackedDomainFilter($builder, $websiteId);

        $row = $builder
            ->get()
            ->getRowArray();

        return (int) ($row['count'] ?? 0);
    }

    /**
     * Get total visits for a website in a date range
     */
    public function getTotalVisits(int $websiteId, string $startDate, string $endDate): int
    {
        $builder = $this->builder()
            ->where('website_id', $websiteId)
            ->where('timestamp >=', $startDate)
            ->where('timestamp <=', $endDate);

        $this->applyTrackedDomainFilter($builder, $websiteId);

        return (int) $builder->countAllResults();
    }

    /**
     * Get top pages by visit count
     */
    public function getTopPages(int $websiteId, int $limit = 10): array
    {
        $builder = $this->builder()
            ->select('url, COUNT(*) as visits')
            ->where('website_id', $websiteId)
            ->groupBy('url')
            ->orderBy('visits', 'DESC')
            ->limit($limit);

        $this->applyTrackedDomainFilter($builder, $websiteId);

        return $builder->get()->getResultArray();
    }

    /**
     * Get bottom pages by visit count
     */
    public function getBottomPages(int $websiteId, int $limit = 10): array
    {
        $builder = $this->builder()
            ->select('url, COUNT(*) as visits')
            ->where('website_id', $websiteId)
            ->groupBy('url')
            ->orderBy('visits', 'ASC')
            ->limit($limit);

        $this->applyTrackedDomainFilter($builder, $websiteId);

        return $builder->get()->getResultArray();
    }

    /**
     * Get daily visit counts for timeline
     */
    public function getDailyVisits(int $websiteId, string $startDate, string $endDate): array
    {
        $builder = $this->builder()
            ->select("DATE(timestamp) as date, COUNT(*) as visits")
            ->where('website_id', $websiteId)
            ->where('timestamp >=', $startDate)
            ->where('timestamp <=', $endDate)
            ->groupBy('DATE(timestamp)')
            ->orderBy('date', 'ASC');

        $this->applyTrackedDomainFilter($builder, $websiteId);

        return $builder->get()->getResultArray();
    }

    /**
     * Get total visits for a website for all recorded time
     */
    public function getTotalVisitsAllTime(int $websiteId): int
    {
        $builder = $this->builder()->where('website_id', $websiteId);
        $this->applyTrackedDomainFilter($builder, $websiteId);

        return (int) $builder->countAllResults();
    }

    /**
     * Get total unique visitors for a website for all recorded time
     */
    public function getTotalUniqueVisitorsAllTime(int $websiteId): int
    {
        $builder = $this->db->table('visits')
            ->select('COUNT(DISTINCT visitor_id) as count', false)
            ->where('website_id', $websiteId);

        $this->applyTrackedDomainFilter($builder, $websiteId);

        $row = $builder
            ->get()
            ->getRowArray();

        return (int) ($row['count'] ?? 0);
    }

    /**
     * Get monthly visit and unique visitor aggregates.
     *
     * @return array<int, array{month_key: string, visits: string|int, unique_visitors: string|int}>
     */
    public function getMonthlyStats(int $websiteId, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->db->table('visits')
            ->select('SUBSTR(timestamp, 1, 7) as month_key, COUNT(*) as visits, COUNT(DISTINCT visitor_id) as unique_visitors', false)
            ->where('website_id', $websiteId);

        $this->applyTrackedDomainFilter($builder, $websiteId);

        if ($startDate !== null) {
            $builder->where('timestamp >=', $startDate);
        }

        if ($endDate !== null) {
            $builder->where('timestamp <=', $endDate);
        }

        return $builder
            ->groupBy('month_key')
            ->orderBy('month_key', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Restrict report queries to URLs from the website's tracked domain.
     */
    private function applyTrackedDomainFilter(BaseBuilder $builder, int $websiteId): void
    {
        $allowedPrefixes = $this->getAllowedUrlPrefixes($websiteId);

        if ($allowedPrefixes === []) {
            $builder->where('1 = 0', null, false);
            return;
        }

        $builder->groupStart();
        $isFirst = true;

        foreach ($allowedPrefixes as $prefix) {
            if ($isFirst) {
                $builder->where('url =', $prefix);
                $builder->orWhere('url LIKE', $prefix . '/%');
                $builder->orWhere('url LIKE', $prefix . ':%');
                $isFirst = false;
                continue;
            }

            $builder->orWhere('url =', $prefix);
            $builder->orWhere('url LIKE', $prefix . '/%');
            $builder->orWhere('url LIKE', $prefix . ':%');
        }

        $builder->groupEnd();
    }

    /**
     * Build allowed URL prefixes for the tracked website domain.
     *
     * @return list<string>
     */
    private function getAllowedUrlPrefixes(int $websiteId): array
    {
        if (array_key_exists($websiteId, $this->allowedUrlPrefixes)) {
            return $this->allowedUrlPrefixes[$websiteId];
        }

        $website = $this->db->table('websites')
            ->select('url')
            ->where('id', $websiteId)
            ->get()
            ->getRowArray();

        $websiteUrl = (string) ($website['url'] ?? '');
        $host = strtolower((string) parse_url($websiteUrl, PHP_URL_HOST));
        $host = trim($host, '.');

        if ($host === '') {
            $this->allowedUrlPrefixes[$websiteId] = [];
            return [];
        }

        $hosts = [$host];
        if (str_starts_with($host, 'www.')) {
            $hosts[] = substr($host, 4);
        } else {
            $hosts[] = 'www.' . $host;
        }

        $hosts = array_values(array_unique(array_filter($hosts, static fn (string $value): bool => $value !== '')));

        $prefixes = [];
        foreach ($hosts as $allowedHost) {
            $prefixes[] = 'http://' . $allowedHost;
            $prefixes[] = 'https://' . $allowedHost;
        }

        $this->allowedUrlPrefixes[$websiteId] = array_values(array_unique($prefixes));

        return $this->allowedUrlPrefixes[$websiteId];
    }

    /**
     * Get average monthly visits and unique visitors over months with data.
     *
     * @return array{average_visits: float, average_unique_visitors: float}
     */
    public function getAverageMonthlyStats(int $websiteId): array
    {
        $monthlyStats = $this->getMonthlyStats($websiteId);
        $monthsWithData = count($monthlyStats);

        if ($monthsWithData === 0) {
            return [
                'average_visits' => 0.0,
                'average_unique_visitors' => 0.0,
            ];
        }

        $totalVisits = 0;
        $totalUniqueVisitors = 0;

        foreach ($monthlyStats as $row) {
            $totalVisits += (int) ($row['visits'] ?? 0);
            $totalUniqueVisitors += (int) ($row['unique_visitors'] ?? 0);
        }

        return [
            'average_visits' => $totalVisits / $monthsWithData,
            'average_unique_visitors' => $totalUniqueVisitors / $monthsWithData,
        ];
    }

    /**
     * Get visits and unique visitors for the previous calendar month.
     *
     * @return array{visits: int, unique_visitors: int}
     */
    public function getLastMonthStats(int $websiteId): array
    {
        $startDate = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $endDate = date('Y-m-t 23:59:59', strtotime('last day of last month'));

        return [
            'visits' => $this->getTotalVisits($websiteId, $startDate, $endDate),
            'unique_visitors' => $this->getUniqueVisitors($websiteId, $startDate, $endDate),
        ];
    }
}