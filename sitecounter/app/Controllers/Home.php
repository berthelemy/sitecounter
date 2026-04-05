<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // If not installed, go to installer
        $installModel = new \App\Models\InstallModel();
        if (!$installModel->isInstalled()) {
            return redirect()->to('/install');
        }

        // If logged in, go to dashboard
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        // Otherwise go to login
        return redirect()->to('/login');
    }

    public function setLanguage(string $locale)
    {
        $supported = config('App')->supportedLocales ?? ['en'];

        if (!in_array($locale, $supported, true)) {
            return redirect()->to('/dashboard');
        }

        $this->response->setCookie('sitecounter_locale', $locale, YEAR, '', '/', '', null, true, 'Lax');

        $redirect = $this->request->getGet('redirect') ?? '/dashboard';
        $parts = parse_url($redirect);

        // Prevent open redirects; only allow local relative paths.
        if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || !str_starts_with($redirect, '/')) {
            $redirect = '/dashboard';
        }

        $response = redirect()->to($redirect);
        $response->setCookie('sitecounter_locale', $locale, YEAR, '', '/', '', false, true, 'Lax');

        return $response;
    }
}
