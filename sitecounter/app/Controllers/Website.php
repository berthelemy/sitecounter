<?php

namespace App\Controllers;

use App\Models\WebsiteModel;
use App\Models\VisitModel;

class Website extends BaseController
{
    protected $websiteModel;

    public function __construct()
    {
        $this->websiteModel = new WebsiteModel();
    }

    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $websites = $this->websiteModel->getUserWebsites($user->id);

        return view('dashboard/websites/index', [
            'websites' => $websites,
            'user' => $user,
        ]);
    }

    public function create()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        return view('dashboard/websites/create', [
            'user' => auth()->user(),
        ]);
    }

    public function store()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();

        $rules = [
            'name' => 'required|min_length[1]|max_length[255]',
            'url' => 'required|valid_url|max_length[255]',
        ];

        $messages = [
            'name' => [
                'required'    => lang('SiteCounter.messages.website_name_required'),
                'min_length'  => lang('SiteCounter.messages.website_name_required'),
                'max_length'  => lang('SiteCounter.messages.website_name_max_length'),
            ],
            'url' => [
                'required'    => lang('SiteCounter.messages.website_url_required'),
                'valid_url'   => lang('SiteCounter.messages.website_url_valid'),
                'max_length'  => lang('SiteCounter.messages.website_url_max_length'),
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'url' => $this->request->getPost('url'),
            'token' => $this->websiteModel->generateToken(),
            'user_id' => $user->id,
        ];

        if ($this->websiteModel->insert($data)) {
            return redirect()->to('/dashboard/websites')->with('success', lang('SiteCounter.messages.website_created'));
        } else {
            return redirect()->back()->withInput()->with('errors', $this->websiteModel->errors());
        }
    }

    public function show($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $website = $this->websiteModel->where('id', $id)->where('user_id', $user->id)->first();

        if (!$website) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Generate tracking script options
        $trackingScripts = $this->generateTrackingScripts($website);

        return view('dashboard/websites/show', [
            'website' => $website,
            'trackingScripts' => $trackingScripts,
            'user' => $user,
        ]);
    }

    public function edit($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $website = $this->websiteModel->where('id', $id)->where('user_id', $user->id)->first();

        if (!$website) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('dashboard/websites/edit', [
            'website' => $website,
            'user' => $user,
        ]);
    }

    public function update($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $website = $this->websiteModel->where('id', $id)->where('user_id', $user->id)->first();

        if (!$website) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name' => 'required|min_length[1]|max_length[255]',
            'url' => 'required|valid_url|max_length[255]',
        ];

        $messages = [
            'name' => [
                'required'    => lang('SiteCounter.messages.website_name_required'),
                'min_length'  => lang('SiteCounter.messages.website_name_required'),
                'max_length'  => lang('SiteCounter.messages.website_name_max_length'),
            ],
            'url' => [
                'required'    => lang('SiteCounter.messages.website_url_required'),
                'valid_url'   => lang('SiteCounter.messages.website_url_valid'),
                'max_length'  => lang('SiteCounter.messages.website_url_max_length'),
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'url' => $this->request->getPost('url'),
        ];

        if ($this->websiteModel->update($website['id'], $data)) {
            return redirect()->to('/dashboard/websites')->with('success', lang('SiteCounter.messages.website_updated'));
        } else {
            return redirect()->back()->withInput()->with('errors', $this->websiteModel->errors());
        }
    }

    public function delete($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $website = $this->websiteModel->where('id', $id)->where('user_id', $user->id)->first();

        if (!$website) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->websiteModel->delete($website['id'])) {
            return redirect()->to('/dashboard/websites')->with('success', lang('SiteCounter.messages.website_deleted'));
        } else {
            return redirect()->back()->withInput()->with('errors', $this->websiteModel->errors());
        }
    }

    public function report($id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $website = $this->websiteModel->where('id', $id)->where('user_id', $user->id)->first();

        if (!$website) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $visitModel = new VisitModel();

        $endDate   = date('Y-m-d 23:59:59');
        $startDate = date('Y-m-d 00:00:00', strtotime('-29 days'));

        $totalVisits    = $visitModel->getTotalVisits($website['id'], $startDate, $endDate);
        $uniqueVisitors = $visitModel->getUniqueVisitors($website['id'], $startDate, $endDate);
        $topPages       = $visitModel->getTopPages($website['id'], 10);
        $bottomPages    = $visitModel->getBottomPages($website['id'], 10);
        $dailyVisits    = $visitModel->getDailyVisits($website['id'], $startDate, $endDate);
        $averageStats   = $visitModel->getAverageMonthlyStats((int) $website['id']);
        $lastMonthStats = $visitModel->getLastMonthStats((int) $website['id']);

        return view('dashboard/websites/report', [
            'website'        => $website,
            'user'           => $user,
            'totalVisits'    => $totalVisits,
            'uniqueVisitors' => $uniqueVisitors,
            'totalVisitsAllTime' => $visitModel->getTotalVisitsAllTime((int) $website['id']),
            'totalUniqueVisitorsAllTime' => $visitModel->getTotalUniqueVisitorsAllTime((int) $website['id']),
            'averageVisitsPerMonth' => $averageStats['average_visits'],
            'averageUniqueVisitorsPerMonth' => $averageStats['average_unique_visitors'],
            'visitsLastMonth' => $lastMonthStats['visits'],
            'uniqueVisitorsLastMonth' => $lastMonthStats['unique_visitors'],
            'topPages'       => $topPages,
            'bottomPages'    => $bottomPages,
            'dailyVisits'    => $dailyVisits,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
        ]);
    }

    private function generateTrackingScripts($website)
    {
        $token = htmlspecialchars((string) $website['token'], ENT_QUOTES, 'UTF-8');
        $scriptUrl = rtrim(base_url('js/sitecounter-tracker.js'), '/');
        $trackEndpoint = rtrim(base_url('track'), '/');

        $shortScript = <<<SCRIPT
<script async src="{$scriptUrl}" data-sitecounter-token="{$token}"></script>
SCRIPT;

        $explicitScript = <<<SCRIPT
<script async src="{$scriptUrl}" data-sitecounter-token="{$token}" data-sitecounter-endpoint="{$trackEndpoint}"></script>
SCRIPT;

        return [
            'short' => $shortScript,
            'explicit' => $explicitScript,
        ];
    }
}
