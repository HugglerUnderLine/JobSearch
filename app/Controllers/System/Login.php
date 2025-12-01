<?php 

namespace App\Controllers\System;
use App\Controllers\BaseController;
use App\Models\System\BlackListModel;
use App\Models\Users\UserModel;
use App\Models\LoggedUsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController {
    #|*****************************|
    #|* Session                   *|
    #|*****************************|
    public $session;
    public function __construct() {
        $this->session = session();
    }


    #|*****************************************|
    #|* Index -> Return view Login            *|
    #|*****************************************|
    public function index() {
        return view('login', ['no_banner' => true, 'no_footer' => true]);
    }


    #|*****************************|
    #|* Auth User / Login         *|
    #|*****************************|
    public function auth_user() {
        try {
            log_message('info', "\n\n====== [LOGIN] ======\n");

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message("info", "[LOGIN] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $username = esc(strval($data['username'] ?? ''));
            $password = strval($data['password'] ?? '');

            $username = substr($username, 0, 20);
            $password = substr($password, 0, 256);

            # Incomplete login credentials
            if (empty(trim($username)) || empty(trim($password))) {
                log_message('warning', '[LOGIN] Empty credentials');
                log_message('info', "\n\n====== [END LOGIN] ======\n");

                return $this->setResponse(401, "Invalid Credentials.");
            }

            $userModel = new UserModel();
            $user = $userModel->authUser($username);

            if (empty($user) || !password_verify($password, $user['password'])) {
                # invalid
                log_message('warning', '[LOGIN] Invalid credentials for: ' . $username);
                log_message('info', "\n\n====== [END LOGIN] ======\n");

               return $this->setResponse(401, "Invalid Credentials.");
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
            } catch (\Throwable $ex) {
                log_message('error', '[LOGIN] JWT Generator Error' . $e->getMessage());
                log_message('info', "\n\n====== [END LOGIN] ======\n");
                throw $ex;
            }

            log_message('info', "[LOGIN] Successful login for user: {$username}.");
            log_message('info', "[LOGIN] Decoded JSON: " . json_encode($decoded, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END LOGIN] ======\n");

            $loggedUsersModel = new LoggedUsersModel();
            $loggedUsersModel->insert([
                'jwt_token'     => $token,
                'user_id'       => $user['user_id'],
                'username'      => $user['username'],
                'name'          => $user['name'],
                'email'         => $user['email'],
                'account_role'  => $user['account_role'],
                'ip'            => $this->request->getIPAddress()
            ]);

            return $this->response->setStatusCode(200)->setContentType('application/json')->setJSON([
                'token' => $token,
                'expires_in' => $decoded['exp'] ?? ""
            ]);

        } catch (\Throwable $e) {
            log_message('error', "[LOGIN] Error: " . $e->getMessage());
            log_message('info', "\n\n====== [END LOGIN] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* Logout User               *|
    #|*****************************|
    public function logout() {
        try {
            log_message('info', "\n\n====== [LOGOUT] ======\n");

            # Capture the Authorization header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[LOGOUT] Received Authorization Header: ' . ($authHeader ?: 'NONE'));
            $token = null;

            # Extract Bearer token from Authorization header
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[LOGOUT] Token extracted from Authorization header.');

            } else {
                log_message('warning', '[LOGOUT] No Bearer token found in Authorization header. Checking POST/JSON body...');

                # Try to get JSON
                $data = $this->request->getJSON(true);

                # If it came empty, try body as array
                if (empty($data)) {
                    $raw = $this->request->getBody();
                    $data = json_decode($raw, true) ?? [];
                }

                log_message('info', "[LOGOUT] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

                $token = $data['token'] ?? null;
            }

            # Return error if token is still missing
            if (empty($token)) {
                log_message('warning', '[LOGOUT] No token provided in request.');
                log_message('info', "\n\n====== [END LOGOUT] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            log_message('debug', '[LOGOUT] Full token before decode: ' . $token);

            # Load helpers and models
            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[LOGOUT] Malformed JWT Token.');
                log_message('info', "\n\n====== [END LOGOUT] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode the JWT token
            $decoded = jwt_decode($token);

            log_message('info', '[LOGOUT] Decoded token: ' . json_encode($decoded));

            # Validate token structure
            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[LOGOUT] Invalid token structure.');
                log_message('info', "\n\n====== [END LOGOUT] ======\n");
                
                return $this->setResponse(401, "Invalid token.");
            }

            // log_message('info', 'Step 1: Token decoded successfully.');

            # Check if the token is already blacklisted
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', "[LOGOUT] Blacklisted Token given.");
                log_message('info', "\n\n====== [END LOGOUT] ======\n");

                return $this->setResponse(401, "Invalid token.");
            }

            $sub = $decoded['sub'];
            # Add the token to blacklist
            $blackListModel->add_token($token);

            # Confirm logout success
            log_message('info', '[LOGOUT] Token blacklisted for user: ' . ($decoded['username'] ?? 'unknown'));

            log_message('info', "[LOGOUT] Logout successful.");
            log_message('info', "\n\n====== [END LOGOUT] ======\n");

            $loggedUsersModel = new LoggedUsersModel();
            $loggedUsersModel
                ->where('jwt_token', $token)
                ->delete();

            return $this->setResponse(200, "Logout successful.");

        } catch (\Throwable $e) {
            log_message('error', "[LOGOUT] Error: " . $e->getMessage());
            log_message('info', "\n\n====== [END LOGOUT] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }

    }


    #|*****************************|
    #|* JSON Response Builder     *|
    #|*****************************|
    private function setResponse($code, $message) {
        return $this->response->setStatusCode($code)
                              ->setContentType('application/json')
                              ->setJSON([
                                    'message' => $message
                                ]);
    }
}

