<?php

use App\Models\WebsiteModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class WebsiteModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = null;

    public function testGenerateTokenReturnsHex64(): void
    {
        $model = new WebsiteModel();
        $token = $model->generateToken();

        $this->assertSame(64, strlen($token));
        $this->assertSame(1, preg_match('/^[a-f0-9]{64}$/', $token));
    }

    public function testGetUserWebsitesReturnsOnlyMatchingUserRows(): void
    {
        $model = new WebsiteModel();
        $tokenOne = $model->generateToken();
        $tokenTwo = $model->generateToken();
        $otherToken = $model->generateToken();

        $this->insertUser(101);
        $this->insertUser(202);

        $this->db->table('websites')->insert([
            'name' => 'Alpha',
            'url' => 'https://alpha.example.com',
            'token' => $tokenOne,
            'user_id' => 101,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('websites')->insert([
            'name' => 'Beta',
            'url' => 'https://beta.example.com',
            'token' => $tokenTwo,
            'user_id' => 101,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('websites')->insert([
            'name' => 'Other',
            'url' => 'https://other.example.com',
            'token' => $otherToken,
            'user_id' => 202,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $result = $model->getUserWebsites(101);

        $this->assertCount(2, $result);
        $this->assertSame([101, 101], array_map(static fn ($row) => (int) $row['user_id'], $result));
    }

    public function testValidationFailsForInvalidFields(): void
    {
        $model = new WebsiteModel();

        $saved = $model->save([
            'name' => '',
            'url' => '',
            'token' => 'short',
            'user_id' => 'abc',
        ]);

        $this->assertFalse($saved);

        $errors = $model->errors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('url', $errors);
        $this->assertArrayHasKey('token', $errors);
        $this->assertArrayHasKey('user_id', $errors);
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
