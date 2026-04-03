<?php

namespace App\Controllers;

use App\Models\WebsiteModel;
use App\Models\VisitModel;

class Track extends BaseController
{
    public function index()
    {
        // Only accept POST requests
        if (! $this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Method not allowed',
                ])
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        }

        // Get JSON data from request
        $json = $this->request->getJSON();
        if (!$json) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid JSON',
                ])
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        }

        // Validate required fields
        if (!isset($json->token) || !isset($json->visitor_id) || !isset($json->url)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required fields',
                ])
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        }

        // Find website by token
        $websiteModel = new WebsiteModel();
        $website = $websiteModel->where('token', $json->token)->first();

        if (!$website) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Website not found',
                ])
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        }

        // Create visit record
        $visitModel = new VisitModel();
        $visitData = [
            'website_id' => $website['id'],
            'visitor_id' => $json->visitor_id,
            'url' => $json->url,
            'title' => $json->title ?? null,
            'referrer' => $json->referrer ?? null,
            'user_agent' => $json->user_agent ?? null,
            'screen_resolution' => $json->screen_resolution ?? null,
            'ip_address' => $this->request->getIPAddress(),
            'timestamp' => isset($json->timestamp) ? date('Y-m-d H:i:s', strtotime($json->timestamp)) : date('Y-m-d H:i:s'),
        ];

        try {
            $inserted = $visitModel->insert($visitData);

            if ($inserted === false) {
                $errors = $visitModel->errors();
                log_message('error', 'Failed to save visit (validation): ' . json_encode($errors));

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => $errors,
                    ])
                    ->setHeader('Access-Control-Allow-Origin', '*')
                    ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                    ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
            }

            // Return success with CORS headers
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['status' => 'success'])
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type');

        } catch (\Exception $e) {
            log_message('error', 'Failed to save visit: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Internal server error',
                ])
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        }
    }

    public function options()
    {
        // Handle CORS preflight requests
        return $this->response
            ->setStatusCode(200)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type');
    }
}