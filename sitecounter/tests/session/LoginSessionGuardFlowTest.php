<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class LoginSessionGuardFlowTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $namespace = null;

    public function testAuthenticatedUserPostingLoginIsRedirectedWithoutError(): void
    {
        $email = 'already-logged-in@example.net';
        $userId = $this->createUserWithEmailIdentity($email, 'OldPassword123!');
        $sessionField = (string) setting('Auth.sessionConfig')['field'];

        $response = $this->withSession([
            $sessionField => ['id' => $userId],
        ])->post('/login', [
            'email' => $email,
            'password' => 'OldPassword123!',
        ]);

        $response->assertRedirectTo(config('Auth')->loginRedirect());
        $response->assertSessionMissing('error');
    }

    public function testStaleSessionPayloadIsClearedAndLoginSucceeds(): void
    {
        $email = 'stale-session@example.net';
        $this->createUserWithEmailIdentity($email, 'OldPassword123!');
        $sessionField = (string) setting('Auth.sessionConfig')['field'];

        $response = $this->withSession([
            $sessionField => ['foo' => 'bar'],
        ])->post('/login', [
            'email' => $email,
            'password' => 'OldPassword123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionMissing('error');
    }

    private function createUserWithEmailIdentity(string $email, string $password): int
    {
        $now = date('Y-m-d H:i:s');

        $userData = [
            'username' => 'user_' . bin2hex(random_bytes(4)),
            'active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $this->db->table('users')->insert($userData);
        $userId = (int) $this->db->insertID();

        $this->db->table('auth_identities')->insert([
            'user_id' => $userId,
            'type' => 'email_password',
            'secret' => strtolower(trim($email)),
            'secret2' => service('passwords')->hash($password),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $userId;
    }
}
