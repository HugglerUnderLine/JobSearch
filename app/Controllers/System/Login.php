<?php 

namespace App\Controllers\System;
use App\Controllers\BaseController;
use App\Models\System\BlackListModel;
use App\Models\Users\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController {

    public $session;

    public function __construct() {
        $this->session = session();
    }

    public function index() {
        return view('login', ['no_banner' => true, 'no_footer' => true]);
    }

    public function auth_user() {
        # If the request isn't POST
        if (!$this->request->is('POST')) {
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

        # Detect form-data or JSON
        $data = $this->request->getPost();
        if (empty($data)) {
            try {
                $data = $this->request->getJSON(true) ?? [];
            } catch (\Exception $e) {
                $data = [];
            }
        }

        $clientIP = $this->request->getIPAddress();
        log_message('info', "[REQUEST] IP do cliente: {$clientIP} - Método: {$this->request->getMethod()} - URI: {$this->request->getURI()}");

        $username = esc(strval($data['username'] ?? ''));
        $password = esc(strval($data['password'] ?? ''));

        $username = substr($username, 0, 20);
        $password = substr($password, 0, 256);

        # Default return
        $response = [
            'message' => 'Invalid credentials.'
        ];
        $statusCode = 401;

        # Incomplete login credentials
        if (empty(trim($username)) || empty(trim($password))) {
            log_message('error', '[LOGIN FORM] Empty credentials');
            return $this->response->setStatusCode($statusCode)->setJSON($response);
        }

        $userModel = new \App\Models\Users\UserModel();
        $user = $userModel->authUser($username);

        if (empty($user) || !password_verify($password, $user['password'])) {
            # invalid
            log_message('warning', '[LOGIN FORM] Invalid credentials for: ' . $username);
            return $this->response->setStatusCode($statusCode)->setJSON($response);
        }

        # Loads JWT Helper
        helper('jwt_helper');

        # Build Payload
        $payload = [
            'sub' => (string) $user['user_id'],
            'username' => $user['username'] ?? '',
            'role' => $user['account_role'] ?? ''
        ];

        # Generate JWT Token with default 1 hour (60 min) expiry
        try {
            $token = jwt_generate($payload, 60);
            $decoded = jwt_decode($token);
        } catch (\Throwable $e) {
            log_message('error', '[JWT GENERATION ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }

        log_message('info', "[LOGIN] Successful login for user: {$username}, token: {$token}");

        log_message('info', "[LOGIN] Decoded JSON: " . json_encode($decoded, JSON_PRETTY_PRINT));

        # Returns token
        return $this->response->setStatusCode(200)->setJSON([
            'token' => $token,
            'expires_in' => $decoded['exp'] ?? ""
        ]);
    }

    public function logout() {
        $clientIP = $this->request->getIPAddress();
        log_message('info', "[REQUEST] IP do cliente: {$clientIP} - Método: {$this->request->getMethod()} - URI: {$this->request->getURI()}");

        log_message('info', '[LOGOUT] ===== Starting LOGOUT process =====');

        # If the request isn't POST
        if (!$this->request->is('POST')) {
            log_message('warning', '[LOGOUT] Invalid request method given: ' . $this->request->getMethod());
        
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

        # Capture the Authorization header
        $authHeader = $this->request->getHeaderLine('Authorization');
        log_message('info', '[LOGOUT] Received Authorization Header: ' . ($authHeader ?: 'NONE'));
        $token = null;

        # Extract Bearer token from Authorization header
        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            log_message('info', '[LOGOUT] Token extracted from Authorization header.');
        } else {
            log_message('info', '[LOGOUT] No Bearer token found in Authorization header. Checking POST/JSON body...');
        }

        if (empty($token)) {
            $data = $this->request->getPost();

            log_message('info', '[LOGOUT] POST Data: ' . json_encode($data));

            if (empty($data)) {
                try {
                    $data = $this->request->getJSON(true) ?? [];
                    log_message('info', '[LOGOUT] JSON Data: ' . json_encode($data));
                } catch (\Exception $e) {
                    log_message('error', '[LOGOUT] Error decoding JSON from body: ' . $e->getMessage());
                    $data = [];
                }
            }

            $token = $data['token'] ?? null;
        }

        # Return error if token is still missing
        if (empty($token)) {
            log_message('error', '[LOGOUT] No token provided in request.');

            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        log_message('debug', '[LOGOUT] Full token before decode: ' . $token);

        # Load helpers and models
        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[LOGOUT] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode the JWT token
            $decoded = jwt_decode($token);

            log_message('info', '[LOGOUT] Decoded token: ' . json_encode($decoded));

            # Validate token structure
            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[LOGOUT] Invalid token structure.');

                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            // log_message('info', 'Step 1: Token decoded successfully.');

            # Check if the token is already blacklisted
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            // log_message('info', 'Step 2: Token is not blacklisted.');

            # Add the token to blacklist
            $inserted = $blackListModel->add_token($token);
            if (!$inserted) {
                log_message('error', '[LOGOUT] Failed to insert token into blacklist.');
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Internal Server Error.'
                ]);
            }

            # Confirm logout success
            log_message('info', '[LOGOUT] Token blacklisted for user: ' . ($decoded['username'] ?? 'unknown'));

            // log_message('info', 'Step 3: Token blacklisted successfully.');

            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'Logout successful.'
            ]);

        } catch (\Throwable $e) {
            # Handle unexpected internal errors
            log_message('error', '[LOGOUT ERROR] ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }


}

