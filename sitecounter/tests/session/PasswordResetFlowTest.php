<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class PasswordResetFlowTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $namespace = null;

    public function testMagicLoginEnablesPasswordResetMode(): void
    {
        $userId = $this->createUserWithEmailIdentity('mark@berthelemy.net', 'OldPassword123!');
        $tempExpiresAt = time() + 300;

        $response = $this->withSession([
            'user' => ['id' => $userId],
            'magicLogin' => true,
            '__ci_vars' => ['magicLogin' => $tempExpiresAt],
        ])->get('/dashboard/profile');

        $response->assertOK();
        $response->assertSessionHas('password_reset_mode', true);
        $response->assertSessionHas('info');
    }

    public function testResetModeAllowsChangingPasswordWithoutCurrentPassword(): void
    {
        $userId = $this->createUserWithEmailIdentity('mark@berthelemy.net', 'OldPassword123!');

        $response = $this->withSession([
            'user' => ['id' => $userId],
            'password_reset_mode' => true,
        ])->post('/dashboard/profile/password', [
            'new_password' => 'NewPassword123!',
            'new_password_confirm' => 'NewPassword123!',
        ]);

        $response->assertRedirectTo('/dashboard/profile');
        $response->assertSessionHas('password_success', lang('SiteCounter.messages.password_changed'));
        $response->assertSessionMissing('password_reset_mode');

        $identity = $this->db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->get()
            ->getRowArray();

        $this->assertNotNull($identity);
        $this->assertTrue(service('passwords')->verify('NewPassword123!', $identity['secret2']));
    }

    public function testRegularPasswordChangeStillRequiresCurrentPassword(): void
    {
        $userId = $this->createUserWithEmailIdentity('mark@berthelemy.net', 'OldPassword123!');

        $response = $this->withSession([
            'user' => ['id' => $userId],
        ])->post('/dashboard/profile/password', [
            'new_password' => 'NewPassword123!',
            'new_password_confirm' => 'NewPassword123!',
        ]);

        $response->assertRedirectTo('/dashboard/profile');
        $response->assertSessionHas('password_errors');

        $errors = $_SESSION['password_errors'] ?? [];
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('current_password', $errors);
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

        $userFields = array_map(static fn($field) => $field->name, $this->db->getFieldData('users'));

        if (in_array('firstname', $userFields, true)) {
            $userData['firstname'] = 'Mark';
        }

        if (in_array('lastname', $userFields, true)) {
            $userData['lastname'] = 'Berthelemy';
        }

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
