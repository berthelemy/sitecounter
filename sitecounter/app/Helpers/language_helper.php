<?php

if (!function_exists('lang_switcher')) {
    /**
     * Generate language switcher HTML
     */
    function lang_switcher(): string
    {
        $currentLang = service('request')->getLocale();
        $supportedLocales = config('App')->supportedLocales ?? ['en'];

        $html = '<div class="dropdown">';
        $html .= '<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">';
        $html .= '<i class="fas fa-globe"></i> ' . strtoupper($currentLang);
        $html .= '</button>';
        $html .= '<ul class="dropdown-menu">';

        $uri = service('request')->getUri();
        $path = '/' . ltrim($uri->getPath(), '/');
        $query = $uri->getQuery();
        $redirect = $path . ($query ? ('?' . $query) : '');

        foreach ($supportedLocales as $locale) {
            $active = ($locale === $currentLang) ? ' active' : '';
            $url = site_url('lang/' . $locale) . '?redirect=' . rawurlencode($redirect);

            $html .= '<li><a class="dropdown-item' . $active . '" href="' . $url . '">' . strtoupper($locale) . '</a></li>';
        }

        $html .= '</ul></div>';

        return $html;
    }
}