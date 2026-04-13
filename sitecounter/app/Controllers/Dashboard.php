<?php

namespace App\Controllers;

use App\Models\VisitModel;
use App\Models\WebsiteModel;
use CodeIgniter\Shield\Entities\User;

class Dashboard extends BaseController
{
    /**
     * Promote Shield's magic-link tempdata to a session flag
     * so the user can reset password once without current password.
     */
    private function activatePasswordResetModeFromMagicLink(): void
    {
        if (! session()->getTempdata('magicLogin')) {
            return;
        }

        session()->set('password_reset_mode', true);
        session()->removeTempdata('magicLogin');
        session()->setFlashdata('info', lang('SiteCounter.messages.magic_link_password_reset_ready'));
    }

    public function index()
    {
        // Check if user is logged in
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $this->activatePasswordResetModeFromMagicLink();

        $user = auth()->user();
        $userModel = new \App\Models\UserModel();
        $websiteModel = new WebsiteModel();
        $visitModel = new VisitModel();

        $websites = $websiteModel->getUserWebsites($user->id);
        $dashboardWebsites = [];

        foreach ($websites as $website) {
            $monthlyStats = $visitModel->getMonthlyStats(
                (int) $website['id'],
                date('Y-m-01 00:00:00', strtotime('-5 months')),
                date('Y-m-t 23:59:59')
            );

            $monthlyMap = [];
            foreach ($monthlyStats as $row) {
                $monthlyMap[(string) ($row['month_key'] ?? '')] = (int) ($row['visits'] ?? 0);
            }

            $chartLabels = [];
            $chartValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthKey = date('Y-m', strtotime("-{$i} months"));
                $chartLabels[] = date('M Y', strtotime($monthKey . '-01'));
                $chartValues[] = $monthlyMap[$monthKey] ?? 0;
            }

            $averageStats = $visitModel->getAverageMonthlyStats((int) $website['id']);
            $lastMonthStats = $visitModel->getLastMonthStats((int) $website['id']);

            $dashboardWebsites[] = [
                'website' => $website,
                'chart' => [
                    'labels' => $chartLabels,
                    'values' => $chartValues,
                ],
                'totalVisits' => $visitModel->getTotalVisitsAllTime((int) $website['id']),
                'totalUniqueVisitors' => $visitModel->getTotalUniqueVisitorsAllTime((int) $website['id']),
                'averageVisitsPerMonth' => $averageStats['average_visits'],
                'averageUniqueVisitorsPerMonth' => $averageStats['average_unique_visitors'],
                'visitsLastMonth' => $lastMonthStats['visits'],
                'uniqueVisitorsLastMonth' => $lastMonthStats['unique_visitors'],
            ];
        }

        return view('dashboard/index', [
            'user' => $user,
            'fullName' => $userModel->getFullName($user),
            'dashboardWebsites' => $dashboardWebsites,
        ]);
    }

    public function profile()
    {
        // Check if user is logged in
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $this->activatePasswordResetModeFromMagicLink();

        $user = auth()->user();
        $userModel = new \App\Models\UserModel();

        return view('dashboard/profile', [
            'user' => $user,
            'fullName' => $userModel->getFullName($user),
            'passwordResetMode' => (bool) session('password_reset_mode'),
        ]);
    }

    public function updateProfile()
    {
        // Check if user is logged in
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $userModel = new \App\Models\UserModel();
        $db = \Config\Database::connect();

        $rules = [
            'firstname' => 'required|alpha_space|min_length[2]|max_length[50]',
            'lastname' => 'required|alpha_space|min_length[2]|max_length[50]',
            'email' => 'required|valid_email',
        ];

        $messages = [
            'firstname' => [
                'required'    => lang('SiteCounter.messages.firstname_required'),
                'alpha_space' => lang('SiteCounter.messages.firstname_alpha_space'),
                'min_length'  => lang('SiteCounter.messages.firstname_min_length'),
                'max_length'  => lang('SiteCounter.messages.firstname_max_length'),
            ],
            'lastname' => [
                'required'    => lang('SiteCounter.messages.lastname_required'),
                'alpha_space' => lang('SiteCounter.messages.lastname_alpha_space'),
                'min_length'  => lang('SiteCounter.messages.lastname_min_length'),
                'max_length'  => lang('SiteCounter.messages.lastname_max_length'),
            ],
            'email' => [
                'required'    => lang('SiteCounter.messages.email_required'),
                'valid_email' => lang('SiteCounter.messages.email_valid'),
            ],
        ];

        $inputData = [
            'firstname' => trim((string) $this->request->getPost('firstname')),
            'lastname'  => trim((string) $this->request->getPost('lastname')),
            'email'     => trim((string) $this->request->getPost('email')),
        ];

        if (! $this->validateData($inputData, $rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower($inputData['email']);
        $otherEmailIdentities = $db->table('auth_identities')
            ->select('secret')
            ->where('type', 'email_password')
            ->where('user_id !=', $user->id)
            ->get()
            ->getResultArray();

        $emailInUse = false;
        foreach ($otherEmailIdentities as $identityRow) {
            $existingEmail = strtolower(trim((string) ($identityRow['secret'] ?? '')));
            if ($existingEmail === $email) {
                $emailInUse = true;
                break;
            }
        }

        if ($emailInUse) {
            return redirect()->back()->withInput()->with('errors', [
                'email' => lang('SiteCounter.messages.email_already_registered'),
            ]);
        }

        $userData = [
            'firstname' => preg_replace('/\s+/', ' ', $inputData['firstname']) ?? $inputData['firstname'],
            'lastname' => preg_replace('/\s+/', ' ', $inputData['lastname']) ?? $inputData['lastname'],
        ];

        $db->transStart();
        $userUpdated = $userModel->update($user->id, $userData);
        $identityUpdated = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->update([
                'secret' => $email,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        $db->transComplete();

        if ($userUpdated && $identityUpdated && $db->transStatus()) {
            return redirect()->to('/dashboard/profile')->with('success', lang('SiteCounter.messages.profile_updated'));
        }

        return redirect()->back()->withInput()->with('error', lang('SiteCounter.messages.profile_update_failed'));
    }

    public function changePassword()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $passwordResetMode = (bool) session('password_reset_mode');

        $rules = $passwordResetMode
            ? [
                'new_password'         => 'required|min_length[8]',
                'new_password_confirm' => 'required|matches[new_password]',
            ]
            : [
                'current_password'     => 'required',
                'new_password'         => 'required|min_length[8]',
                'new_password_confirm' => 'required|matches[new_password]',
            ];

        $messages = [
            'current_password' => [
                'required' => lang('SiteCounter.messages.current_password_required'),
            ],
            'new_password' => [
                'required' => lang('SiteCounter.messages.new_password_required'),
                'min_length' => lang('SiteCounter.messages.new_password_min_length'),
            ],
            'new_password_confirm' => [
                'required' => lang('SiteCounter.messages.new_password_confirm_required'),
                'matches' => lang('SiteCounter.messages.new_password_confirm_matches'),
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->to('/dashboard/profile')
                ->withInput()
                ->with('password_errors', $this->validator->getErrors());
        }

        $newPassword     = $this->request->getPost('new_password');

        $user = auth()->user();
        if (! $passwordResetMode) {
            $currentPassword = $this->request->getPost('current_password');
            $validPassword = service('passwords')->verify($currentPassword, $user->password_hash);

            if (!$validPassword) {
                return redirect()->to('/dashboard/profile')
                    ->with('password_errors', ['current_password' => lang('SiteCounter.messages.current_password_incorrect')]);
            }
        }

        $users = auth()->getProvider();
        $user->fill(['password' => $newPassword]);
        $users->save($user);

        if ($passwordResetMode) {
            session()->remove('password_reset_mode');
        }

        return redirect()->to('/dashboard/profile')->with('password_success', lang('SiteCounter.messages.password_changed'));
    }

    public function updateLanguage()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $locale = $this->request->getPost('language');
        $supported = config('App')->supportedLocales ?? ['en'];

        if (!in_array($locale, $supported, true)) {
            return redirect()->to('/dashboard/profile')->with('error', lang('SiteCounter.messages.unsupported_language'));
        }

        // Persist locale in a cookie for one year.
        $cookie = new \CodeIgniter\Cookie\Cookie(
            'sitecounter_locale',
            $locale,
            ['expires' => time() + YEAR, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']
        );
        $this->response->setCookie($cookie);

        service('request')->setLocale($locale);

        return redirect()->to('/dashboard/profile')->with('success', lang('SiteCounter.messages.language_saved'));
    }
}