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

        // Generate tracking script
        $trackingScript = $this->generateTrackingScript($website);

        return view('dashboard/websites/show', [
            'website' => $website,
            'trackingScript' => $trackingScript,
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

        return view('dashboard/websites/report', [
            'website'        => $website,
            'user'           => $user,
            'totalVisits'    => $totalVisits,
            'uniqueVisitors' => $uniqueVisitors,
            'topPages'       => $topPages,
            'bottomPages'    => $bottomPages,
            'dailyVisits'    => $dailyVisits,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
        ]);
    }

    private function generateTrackingScript($website)
    {
        $baseUrl = base_url();
        $token = $website['token'];

        return <<<SCRIPT
<script>
(function() {
    function getCookie(name) {
        var cookies = document.cookie ? document.cookie.split('; ') : [];
        for (var i = 0; i < cookies.length; i++) {
            var parts = cookies[i].split('=');
            var key = decodeURIComponent(parts.shift());
            if (key === name) {
                return decodeURIComponent(parts.join('='));
            }
        }
        return null;
    }

    function setCookie(name, value, days) {
        var maxAge = days ? days * 24 * 60 * 60 : 31536000;
        var encoded = encodeURIComponent(name) + '=' + encodeURIComponent(value);

        // Try strict modern attributes first.
        document.cookie = encoded + '; path=/; max-age=' + maxAge + '; SameSite=Lax';

        if (getCookie(name) === value) {
            return true;
        }

        // Fallback for browsers that ignore SameSite in this context.
        document.cookie = encoded + '; path=/; max-age=' + maxAge;

        if (getCookie(name) === value) {
            return true;
        }

        // Last fallback keeps at least a session cookie.
        document.cookie = encoded + '; path=/';

        return getCookie(name) === value;
    }

    function generateUuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0;
            var v = c === 'x' ? r : ((r & 0x3) | 0x8);
            return v.toString(16);
        });
    }

    var cookieName = 'sitecounter_visitor_id';
    var visitorId = getCookie(cookieName);

    // Keep continuity with existing installs that used localStorage first.
    if (!visitorId) {
        visitorId = localStorage.getItem(cookieName);
    }

    if (!visitorId) {
        visitorId = generateUuid();
    }

    var cookieStored = setCookie(cookieName, visitorId, 365);
    localStorage.setItem(cookieName, visitorId);

    if (!cookieStored && window.console && typeof window.console.warn === 'function') {
        window.console.warn('SiteCounter: Could not persist visitor cookie. Using localStorage fallback only.');
    }

    function getPageTitle() {
        var title = '';

        if (typeof document.title === 'string') {
            title = document.title.trim();
        }

        if (!title) {
            var titleEl = document.querySelector('title');
            if (titleEl && typeof titleEl.textContent === 'string') {
                title = titleEl.textContent.trim();
            }
        }

        if (!title) {
            title = window.location.pathname;
        }

        return title;
    }

    function sendVisit() {
        var data = {
            token: '{$token}',
            visitor_id: visitorId,
            url: window.location.href,
            title: getPageTitle(),
            referrer: document.referrer,
            user_agent: navigator.userAgent,
            screen_resolution: screen.width + 'x' + screen.height,
            timestamp: new Date().toISOString()
        };

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{$baseUrl}track', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', sendVisit, { once: true });
    } else {
        sendVisit();
    }
})();
</script>
SCRIPT;
    }
}