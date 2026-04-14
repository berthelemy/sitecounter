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

    private function generateTrackingScript($website)
    {
        $baseUrl = base_url();
        $token = $website['token'];

        return <<<SCRIPT
<script>
(function() {
    var consentStorageKey = 'sitecounter_cookie_consent';
    var visitorCookieName = 'sitecounter_visitor_id';

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

    function getConsentChoice() {
        try {
            return window.localStorage.getItem(consentStorageKey);
        } catch (e) {
            return null;
        }
    }

    function saveConsentChoice(choice) {
        try {
            window.localStorage.setItem(consentStorageKey, choice);
        } catch (e) {
            // Ignore persistence errors (private mode or blocked storage).
        }
    }

    function createConsentBanner(onAllow, onDeny) {
        if (!document.body) {
            return;
        }

        var banner = document.createElement('div');
        banner.id = 'sitecounter-consent-banner';
        banner.style.position = 'fixed';
        banner.style.bottom = '16px';
        banner.style.left = '16px';
        banner.style.right = '16px';
        banner.style.maxWidth = '640px';
        banner.style.margin = '0 auto';
        banner.style.zIndex = '2147483647';
        banner.style.background = '#1f2937';
        banner.style.color = '#ffffff';
        banner.style.padding = '16px';
        banner.style.borderRadius = '10px';
        banner.style.boxShadow = '0 12px 24px rgba(0, 0, 0, 0.25)';
        banner.style.fontFamily = 'Arial, sans-serif';
        banner.style.fontSize = '14px';
        banner.style.lineHeight = '1.5';

        var text = document.createElement('p');
        text.style.margin = '0 0 12px';
        text.textContent = 'I store a single cookie in your browser so that I can count the number of times you visit this site. I do not who you are, and do not use the cookie for any other purpose, nor is it sold or given to anyone else.';

        var buttonRow = document.createElement('div');
        buttonRow.style.display = 'flex';
        buttonRow.style.gap = '8px';
        buttonRow.style.flexWrap = 'wrap';

        var allowButton = document.createElement('button');
        allowButton.type = 'button';
        allowButton.textContent = 'Allow cookie';
        allowButton.style.border = '0';
        allowButton.style.padding = '8px 12px';
        allowButton.style.borderRadius = '6px';
        allowButton.style.cursor = 'pointer';
        allowButton.style.background = '#10b981';
        allowButton.style.color = '#ffffff';

        var denyButton = document.createElement('button');
        denyButton.type = 'button';
        denyButton.textContent = 'Decline';
        denyButton.style.border = '1px solid #6b7280';
        denyButton.style.padding = '8px 12px';
        denyButton.style.borderRadius = '6px';
        denyButton.style.cursor = 'pointer';
        denyButton.style.background = 'transparent';
        denyButton.style.color = '#ffffff';

        allowButton.addEventListener('click', function() {
            if (banner.parentNode) {
                banner.parentNode.removeChild(banner);
            }
            onAllow();
        });

        denyButton.addEventListener('click', function() {
            if (banner.parentNode) {
                banner.parentNode.removeChild(banner);
            }
            onDeny();
        });

        buttonRow.appendChild(allowButton);
        buttonRow.appendChild(denyButton);
        banner.appendChild(text);
        banner.appendChild(buttonRow);

        document.body.appendChild(banner);
    }

    function ensureVisitorIdCookie() {
        var visitorId = getCookie(visitorCookieName);

        if (!visitorId) {
            visitorId = generateUuid();
        }

        var cookieStored = setCookie(visitorCookieName, visitorId, 365);
        if (!cookieStored) {
            if (window.console && typeof window.console.warn === 'function') {
                window.console.warn('SiteCounter: Cookie consent granted, but visitor cookie could not be persisted.');
            }
            return null;
        }

        return visitorId;
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

    function sendVisit(visitorId) {
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

    function startTrackingAfterConsent() {
        var visitorId = ensureVisitorIdCookie();
        if (!visitorId) {
            return;
        }

        sendVisit(visitorId);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            var consentChoice = getConsentChoice();

            if (consentChoice === 'allow') {
                startTrackingAfterConsent();
                return;
            }

            if (consentChoice === 'deny') {
                return;
            }

            createConsentBanner(function() {
                saveConsentChoice('allow');
                startTrackingAfterConsent();
            }, function() {
                saveConsentChoice('deny');
            });
        }, { once: true });
    } else {
        var consentChoice = getConsentChoice();

        if (consentChoice === 'allow') {
            startTrackingAfterConsent();
        } else if (consentChoice !== 'deny') {
            createConsentBanner(function() {
                saveConsentChoice('allow');
                startTrackingAfterConsent();
            }, function() {
                saveConsentChoice('deny');
            });
        }
    }
})();
</script>
SCRIPT;
    }
}