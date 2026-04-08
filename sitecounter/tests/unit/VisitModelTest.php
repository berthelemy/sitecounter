<?php

use App\Models\VisitModel;
use App\Models\WebsiteModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class VisitModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = null;

    public function testAggregateQueriesReturnExpectedValues(): void
    {
        $this->insertUser(301);

        $websiteModel = new WebsiteModel();
        $websiteId = (int) $websiteModel->insert([
            'name' => 'Tracked',
            'url' => 'https://tracked.example.com',
            'token' => $websiteModel->generateToken(),
            'user_id' => 301,
        ], true);

        $visitModel = new VisitModel();

        $this->insertVisit($websiteId, 'visitor-a', 'https://tracked.example.com/a', '2026-04-01 10:00:00');
        $this->insertVisit($websiteId, 'visitor-a', 'https://tracked.example.com/a', '2026-04-01 12:00:00');
        $this->insertVisit($websiteId, 'visitor-b', 'https://tracked.example.com/b', '2026-04-02 09:00:00');
        $this->insertVisit($websiteId, 'visitor-c', 'https://tracked.example.com/c', '2026-04-03 15:30:00');

        $start = '2026-04-01 00:00:00';
        $end = '2026-04-30 23:59:59';

        $this->assertSame(3, $visitModel->getUniqueVisitors($websiteId, $start, $end));
        $this->assertSame(4, $visitModel->getTotalVisits($websiteId, $start, $end));

        $top = $visitModel->getTopPages($websiteId, 2);
        $bottom = $visitModel->getBottomPages($websiteId, 2);
        $daily = $visitModel->getDailyVisits($websiteId, $start, $end);

        $this->assertCount(2, $top);
        $this->assertSame('https://tracked.example.com/a', $top[0]['url']);
        $this->assertSame('2', (string) $top[0]['visits']);

        $this->assertCount(2, $bottom);
        $this->assertSame('1', (string) $bottom[0]['visits']);

        $this->assertCount(3, $daily);
        $this->assertSame('2026-04-01', $daily[0]['date']);
        $this->assertSame('2', (string) $daily[0]['visits']);
    }

    public function testValidationFailsForInvalidFields(): void
    {
        $visitModel = new VisitModel();

        $saved = $visitModel->save([
            'website_id' => 'abc',
            'visitor_id' => str_repeat('v', 37),
            'url' => '',
            'timestamp' => 'not-a-date',
        ]);

        $this->assertFalse($saved);

        $errors = $visitModel->errors();
        $this->assertArrayHasKey('website_id', $errors);
        $this->assertArrayHasKey('visitor_id', $errors);
        $this->assertArrayHasKey('url', $errors);
        $this->assertArrayHasKey('timestamp', $errors);
    }

    private function insertVisit(int $websiteId, string $visitorId, string $url, string $timestamp): void
    {
        $this->db->table('visits')->insert([
            'website_id' => $websiteId,
            'visitor_id' => $visitorId,
            'url' => $url,
            'title' => 'Title',
            'referrer' => null,
            'user_agent' => 'test-agent',
            'screen_resolution' => '1920x1080',
            'ip_address' => '127.0.0.1',
            'timestamp' => $timestamp,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function insertUser(int $userId): void
    {
        $this->db->table('users')->insert([
            'id' => $userId,
            'username' => 'user_' . $userId,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
