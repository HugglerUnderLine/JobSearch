<?php

namespace App\Controllers\System;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LoggedUsersModel;
use App\Models\System\BlackListModel;

class Error extends BaseController
{
    public function index()
    {
        //
    }

    public function receive_error() {
        try {
            log_message('info', "\n\n====== [RECEIVE ERROR FALLBACK] ======\n");
            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[RECEIVE ERROR FALLBACK] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[RECEIVE ERROR FALLBACK] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[RECEIVE ERROR FALLBACK] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[RECEIVE ERROR FALLBACK] No token provided.');
                log_message('info', "\n\n====== [END RECEIVE ERROR FALLBACK] ======\n");
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Missing or invalid authorization token.'
                ]);
            }

            log_message('debug', '[RECEIVE ERROR FALLBACK] Full token before decode: ' . $token);

            helper('jwt_helper');

            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[RECEIVE ERROR FALLBACK] Invalid JWT format.');
                log_message('info', "\n\n====== [END RECEIVE ERROR FALLBACK] ======\n");
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[RECEIVE ERROR FALLBACK] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[RECEIVE ERROR FALLBACK] Invalid token structure.');
                log_message('info', "\n\n====== [END RECEIVE ERROR FALLBACK] ======\n");
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[RECEIVE ERROR FALLBACK] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END RECEIVE ERROR FALLBACK] ======\n");
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            log_message('info', "\n\n====== [END RECEIVE ERROR FALLBACK] ======\n");
            return $this->response->setStatusCode(200)->setJSON([
                'message' => "We've received your message."
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', '[RECEIVE ERROR FALLBACK] ' . $e->getMessage());
            log_message('info', "\n\n====== [END RECEIVE ERROR FALLBACK] ======\n");

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
        
    }
}
