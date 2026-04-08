<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Shield\Entities\User as ShieldUser;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;
use RuntimeException;

class InstallModel extends Model
{
    protected $table = 'install';
    protected $primaryKey = 'id';
    protected $allowedFields = ['installed_at', 'version'];

    public function isInstalled(): bool
    {
        // Check if install table exists and has records
        try {
            $result = $this->db->query('SELECT COUNT(*) as count FROM install');
            $row = $result->getRow();
            return $row && $row->count > 0;
        } catch (\Exception $e) {
            // Table doesn't exist yet
            return false;
        }
    }

    /**
     * Run installation for SQLite.
     *
     * @param array<string, mixed> $input
     */
    public function install(array $input = []): bool
    {
        $dbConfig = $this->buildDatabaseConfig($input);
        $this->testDatabaseConnection($dbConfig);
        $this->persistDatabaseConfigToEnv($dbConfig, $input);
        $this->applyRuntimeDatabaseConfig($dbConfig);

        try {
            // Use a fresh migration service so it picks up runtime DB settings.
            $migration = \Config\Services::migrations(null, null, false);
            $migration->setNamespace(null);
            $migration->latest();

            $db = \Config\Database::connect($dbConfig, false);

            $this->createAdminUser($input);

            // Mark as installed
            $db->table('install')->insert([
                'installed_at' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Installation failed: ' . $e->getMessage());
            throw new RuntimeException(lang('SiteCounter.messages.install_error', [$e->getMessage()]));
        }
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function buildDatabaseConfig(array $input): array
    {
        $driver = strtolower(trim((string) ($input['db_driver'] ?? 'sqlite')));

        if ($driver !== 'sqlite') {
            throw new RuntimeException(lang('SiteCounter.messages.install_db_driver_invalid'));
        }

        $database = trim((string) ($input['sqlite_database'] ?? 'sitecounter.db'));
        if ($database === '') {
            throw new RuntimeException(lang('SiteCounter.messages.install_db_sqlite_name_required'));
        }

        return [
            'hostname'   => 'localhost',
            'database'   => $database,
            'username'   => '',
            'password'   => '',
            'DBDriver'   => 'SQLite3',
            'DBPrefix'   => '',
            'DBDebug'    => true,
            'charset'    => 'utf8',
            'DBCollat'   => '',
            'port'       => '',
            'foreignKeys' => true,
            'busyTimeout' => 1000,
            'dateFormat' => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $dbConfig
     */
    private function testDatabaseConnection(array $dbConfig): void
    {
        $sqlitePath = $this->resolveSqlitePath((string) $dbConfig['database']);
        $directory = dirname($sqlitePath);

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException(lang('SiteCounter.messages.install_db_sqlite_dir_failed'));
        }

        try {
            $sqlite = new \SQLite3($sqlitePath);
            $sqlite->close();
        } catch (\Throwable $e) {
            throw new RuntimeException(lang('SiteCounter.messages.install_db_connection_failed', [$e->getMessage()]));
        }
    }

    /**
     * @param array<string, mixed> $dbConfig
     * @param array<string, mixed> $input
     */
    private function persistDatabaseConfigToEnv(array $dbConfig, array $input = []): void
    {
        $envPath = ROOTPATH . '.env';
        $content = file_get_contents($envPath);

        if ($content === false) {
            throw new RuntimeException(lang('SiteCounter.messages.install_env_read_failed'));
        }

        $updates = [
            'database.default.hostname' => (string) ($dbConfig['hostname'] ?? ''),
            'database.default.database' => (string) ($dbConfig['database'] ?? ''),
            'database.default.username' => (string) ($dbConfig['username'] ?? ''),
            'database.default.password' => (string) ($dbConfig['password'] ?? ''),
            'database.default.DBDriver' => (string) ($dbConfig['DBDriver'] ?? ''),
            'database.default.DBPrefix' => (string) ($dbConfig['DBPrefix'] ?? ''),
            'database.default.port'     => (string) ($dbConfig['port'] ?? ''),
        ];

        $appBaseURL = trim((string) ($input['app_base_url'] ?? ''));
        if ($appBaseURL !== '') {
            $updates['app.baseURL'] = $this->normalizeBaseURL($appBaseURL);
        }

        foreach ($updates as $key => $value) {
            $content = $this->upsertEnvValue($content, $key, $value);
        }

        if (file_put_contents($envPath, $content) === false) {
            throw new RuntimeException(lang('SiteCounter.messages.install_env_write_failed'));
        }
    }

    /**
     * @param array<string, mixed> $dbConfig
     */
    private function applyRuntimeDatabaseConfig(array $dbConfig): void
    {
        $runtimeValues = [
            'database.default.hostname' => (string) ($dbConfig['hostname'] ?? ''),
            'database.default.database' => (string) ($dbConfig['database'] ?? ''),
            'database.default.username' => (string) ($dbConfig['username'] ?? ''),
            'database.default.password' => (string) ($dbConfig['password'] ?? ''),
            'database.default.DBDriver' => (string) ($dbConfig['DBDriver'] ?? ''),
            'database.default.DBPrefix' => (string) ($dbConfig['DBPrefix'] ?? ''),
            'database.default.port'     => (string) ($dbConfig['port'] ?? ''),
        ];

        foreach ($runtimeValues as $key => $value) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    private function resolveSqlitePath(string $database): string
    {
        if ($database === ':memory:') {
            return $database;
        }

        if ($database[0] === '/' || str_starts_with($database, ROOTPATH)) {
            return $database;
        }

        return ROOTPATH . $database;
    }

    private function upsertEnvValue(string $content, string $key, string $value): string
    {
        $escapedKey = preg_quote($key, '/');
        $line = $key . ' = ' . $this->formatEnvValue($value);

        if (preg_match('/^#?\s*' . $escapedKey . '\s*=.*$/m', $content) === 1) {
            return preg_replace('/^#?\s*' . $escapedKey . '\s*=.*$/m', $line, $content, 1) ?? $content;
        }

        return rtrim($content) . PHP_EOL . $line . PHP_EOL;
    }

    private function formatEnvValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/^[A-Za-z0-9_:\/.\-]+$/', $value) === 1) {
            return $value;
        }

        return '\'' . str_replace('\'', '\\\'', $value) . '\'';
    }

    private function normalizeBaseURL(string $value): string
    {
        $baseURL = rtrim(trim($value), '/') . '/';

        if (! filter_var($baseURL, FILTER_VALIDATE_URL)) {
            throw new RuntimeException(lang('SiteCounter.messages.install_base_url_invalid'));
        }

        return $baseURL;
    }

    /**
     * @param array<string, mixed> $input
     */
    private function createAdminUser(array $input): void
    {
        $email = trim((string) ($input['admin_email'] ?? ''));
        $password = (string) ($input['admin_password'] ?? '');
        $firstname = trim((string) ($input['admin_firstname'] ?? 'Site'));
        $lastname = trim((string) ($input['admin_lastname'] ?? 'Admin'));

        if ($email === '') {
            throw new RuntimeException(lang('SiteCounter.messages.install_admin_email_required'));
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException(lang('SiteCounter.messages.install_admin_email_invalid'));
        }

        if (mb_strlen($password) < 8) {
            throw new RuntimeException(lang('SiteCounter.messages.install_admin_password_min'));
        }

        if ($firstname === '') {
            $firstname = 'Site';
        }

        if ($lastname === '') {
            $lastname = 'Admin';
        }

        try {
            $users = model(ShieldUserModel::class);
            if (! $users instanceof ShieldUserModel) {
                throw new RuntimeException(lang('SiteCounter.messages.install_admin_create_failed', ['User provider unavailable']));
            }

            $existingUser = $users->findByCredentials(['email' => $email]);
            if ($existingUser !== null) {
                throw new RuntimeException(lang('SiteCounter.messages.email_already_registered'));
            }

            $user = new ShieldUser([
                'username' => 'admin',
                'active'   => 1,
            ]);
            $user->email = $email;
            $user->password = $password;

            $users->save($user);

            $createdUser = $users->findByCredentials(['email' => $email]);
            if ($createdUser === null) {
                throw new RuntimeException('Admin user record was not created.');
            }

            $userModel = new UserModel();
            $userModel->update($createdUser->id, [
                'firstname' => $firstname,
                'lastname'  => $lastname,
            ]);

            $createdUser->addGroup('admin');
        } catch (\Throwable $e) {
            throw new RuntimeException(lang('SiteCounter.messages.install_admin_create_failed', [$e->getMessage()]));
        }
    }
}