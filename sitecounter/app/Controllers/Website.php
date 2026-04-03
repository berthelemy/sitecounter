<?php

namespace App\Controllers;

use App\Models\WebsiteModel;

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

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'url' => $this->request->getPost('url'),
            'token' => $this->websiteModel->generateToken(),
            'user_id' => $user->id,
        ];

        if ($this->websiteModel->insert($data)) {
            return redirect()->to('/dashboard/websites')->with('success', 'Website created successfully.');
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

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'url' => $this->request->getPost('url'),
        ];

        if ($this->websiteModel->update($website['id'], $data)) {
            return redirect()->to('/dashboard/websites')->with('success', 'Website updated successfully.');
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
            return redirect()->to('/dashboard/websites')->with('success', 'Website deleted successfully.');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->websiteModel->errors());
        }
    }

    private function generateTrackingScript($website)
    {
        $baseUrl = base_url();
        $token = $website['token'];

        return <<<SCRIPT
<script>
(function() {
    var visitorId = localStorage.getItem('sitecounter_visitor_id');
    if (!visitorId) {
        visitorId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
        localStorage.setItem('sitecounter_visitor_id', visitorId);
    }

    var data = {
        token: '{$token}',
        visitor_id: visitorId,
        url: window.location.href,
        referrer: document.referrer,
        user_agent: navigator.userAgent,
        screen_resolution: screen.width + 'x' + screen.height,
        timestamp: new Date().toISOString()
    };

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{$baseUrl}track', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));
})();
</script>
SCRIPT;
    }
}