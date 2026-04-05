<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class ProfileUpdateFlowTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $namespace = null;

    public function testProfileUpdateNormalizesNamesAndEmail(): void
    {
        $userId = $this->createUserWithEmailIdentity('owner@example.net', 'OldPassword123!', 'Current', 'User');

        $response = $this->withSession([
            'user' => ['id' => $userId],
        ])->post('/dashboard/profile', [
            'firstname' => '  Jean   Luc  ',
            'lastname' => '  Picard   Senior ',
            'email' => '  Mixed.Case@Example.NET  ',
        ]);

        $response->assertRedirectTo('/dashboard/profile');
        $response->assertSessionHas('success', lang('SiteCounter.messages.profile_updated'));

        $updatedUser = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $updatedIdentity = $this->db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->get()
            ->getRowArray();

        $this->assertIsArray($updatedUser);
        $this->assertSame('Jean Luc', $updatedUser['firstname'] ?? null);
        $this->assertSame('Picard Senior', $updatedUser['lastname'] ?? null);

        $this->assertIsArray($updatedIdentity);
        $this->assertSame('mixed.case@example.net', $updatedIdentity['secret'] ?? null);
    }

    public function testProfileUpdateRejectsCaseInsensitiveDuplicateEmail(): void
    {
        $existingUserId = $this->createUserWithEmailIdentity('duplicate@example.net', 'OldPassword123!', 'Existing', 'User');
        $updatingUserId = $this->createUserWithEmailIdentity('another@example.net', 'OldPassword123!', 'Another', 'User');

        $this->assertNotSame($existingUserId, $updatingUserId);

        $response = $this->withSession([
            'user' => ['id' => $updatingUserId],
        ])->post('/dashboard/profile', [
            'firstname' => 'Another',
            'lastname' => 'User',
            'email' => 'DUPLICATE@EXAMPLE.NET',
        ]);

        $response->assertRedirect();

        $identity = $this->db->table('auth_identities')
            ->where('user_id', $updatingUserId)
            ->where('type', 'email_password')
            ->get()
            ->getRowArray();

        $this->assertIsArray($identity);
        $this->assertSame('another@example.net', $identity['secret'] ?? null);

        $duplicateCount = $this->db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', 'duplicate@example.net')
            ->countAllResults();
        $this->assertSame(1, $duplicateCount);
    }

    private function createUserWithEmailIdentity(string $email, string $password, string $firstname, string $lastname): int
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
            $userData['firstname'] = $firstname;
        }

        if (in_array('lastname', $userFields, true)) {
            $userData['lastname'] = $lastname;
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
