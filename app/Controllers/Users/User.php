<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\System\BlackListModel;
use App\Models\Users\UserModel;
use App\Models\LoggedUsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    #|*****************************|
    #|* Helpers                   *|
    #|*****************************|
    protected $helpers = ['misc_helper'];


    #|*****************************|
    #|* Session                   *|
    #|*****************************|
    public $session;
    public function __construct() {
        $this->session = session();
    }


    #|******************************|
    #|* Validation Rules           *|
    #|******************************|
    protected $userRegistrationRules = [
        'name' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Full Name is required.',
                'min_length' => 'Full Name must be at least 4 characters long.',
                'max_length' => 'Full Name must not exceed 150 characters.',
            ],
        ],
        'username' => [
            'rules' => 'required|min_length[4]|max_length[20]|is_unique[users.username]|no_spaces|no_special_chars',
            'errors' => [
                'required' => 'Username is required.',
                'min_length' => 'Username must be at least 4 characters long.',
                'max_length' => 'Username must not exceed 20 characters.',
                'is_unique' => 'The username is already taken. Please choose another one.',
                'no_spaces' => 'Username cannot contain spaces.',
                'no_special_chars' => 'Username cannot contain special characters.',
            ],
        ],
        'password' => [
            'rules' => 'required|min_length[3]|max_length[20]|no_spaces|no_special_chars',
            'errors' => [
                'required' => 'Password is required.',
                'min_length' => 'Password must be at least 3 characters long.',
                'max_length' => 'Password must not exceed 20 characters.',
                'no_spaces' => 'Password cannot contain spaces.',
                'no_special_chars' => 'Password cannot contain special characters.',
            ],
        ],
        'phone' => [
            'rules' => 'permit_empty|min_length[10]|max_length[14]',
            'errors' => [
                'min_length' => 'Phone number must be at least 10 digits long.',
                'max_length' => 'Phone number must not exceed 14 digits.',
            ],
        ],
        'email' => [
            'rules' => 'permit_empty|valid_email|min_length[10]|max_length[150]',
            'errors' => [
                'valid_email' => 'Please provide a valid email address.',
                'min_length' => 'Email must be at least 10 characters long.',
                'max_length' => 'Email must not exceed 150 characters.',
            ],
        ],
        'experience' => [
            'rules' => 'permit_empty|min_length[10]|max_length[600]',
            'errors' => [
                'min_length' => 'Experience cannot be less than 10 characters.',
                'max_length' => 'Experience must not exceed 600 characters.',
            ],
        ],
        'education' => [
            'rules' => 'permit_empty|min_length[10]|max_length[600]',
            'errors' => [
                'min_length' => 'Education cannot be less than 10 characters.',
                'max_length' => 'Education must not exceed 600 characters.',
            ],
        ],
    ];

    protected $userUpdatenRules = [
        'name' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Full Name is required.',
                'min_length' => 'Full Name must be at least 4 characters long.',
                'max_length' => 'Full Name must not exceed 150 characters.',
            ],
        ],
        'password' => [
            'rules' => 'permit_empty|min_length[3]|max_length[20]|no_spaces|no_special_chars',
            'errors' => [
                'min_length' => 'Password must be at least 3 characters long.',
                'max_length' => 'Password must not exceed 20 characters.',
                'no_spaces' => 'Password cannot contain spaces.',
                'no_special_chars' => 'Password cannot contain special characters.',
            ],
        ],
        'phone' => [
            'rules' => 'permit_empty|min_length[10]|max_length[14]',
            'errors' => [
                'min_length' => 'Phone number must be at least 10 digits long.',
                'max_length' => 'Phone number must not exceed 14 digits.',
            ],
        ],
        'email' => [
            'rules' => 'permit_empty|valid_email|min_length[10]|max_length[150]',
            'errors' => [
                'valid_email' => 'Please provide a valid email address.',
                'min_length' => 'Email must be at least 10 characters long.',
                'max_length' => 'Email must not exceed 150 characters.',
            ],
        ],
        'experience' => [
            'rules' => 'permit_empty|min_length[10]|max_length[600]',
            'errors' => [
                'min_length' => 'Experience cannot be less than 10 characters.',
                'max_length' => 'Experience must not exceed 600 characters.',
            ],
        ],
        'education' => [
            'rules' => 'permit_empty|min_length[10]|max_length[600]',
            'errors' => [
                'min_length' => 'Education cannot be less than 10 characters.',
                'max_length' => 'Education must not exceed 600 characters.',
            ],
        ],
    ];


    #|*****************************************|
    #|* Index -> Return view Profile          *|
    #|*****************************************|
    public function index() {
        return view('system/profile');
    }


    #|****************************************************|
    #|* List Applications -> Return view my_applications *|
    #|****************************************************|
    public function list_applications() {
        return view('user/my_applications');
    }


    #|*****************************|
    #|* Read User Data            *|
    #|*****************************|
    public function read_user_data($user_id = null) {
        try {
            log_message('info', "\n\n====== [READ USER DATA] ======\n");

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[READ USER DATA] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[READ USER DATA] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[READ USER DATA] No token provided.');
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            // log_message('debug', '[READ USER DATA] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[READ USER DATA] Invalid JWT format.');
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[READ USER DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[READ USER DATA] Invalid token structure.');
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[READ USER DATA] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[READ USER DATA] Access denied. Role is not "user".');
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate role: must be 'user'
            if ($sub != $user_id) {
                log_message('warning', "[READ USER DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");
                
                return $this->setResponse(403, "Forbidden.");
            }

            $userModel = new UserModel();
            $userData = $userModel->getUserDataByID($user_id);

            if (empty($userData)) {
                log_message('warning', '[READ USER DATA] User not found. ID: ' . $user_id);
                log_message('info', "\n\n====== [END READ USER DATA] ======\n");

                return $this->setResponse(404, "User not found.");
            }

            # Remove sensitive data
            unset($userData['password'], $userData['user_id'], $userData['account_role']);

            foreach ($userData as $col => &$value) {
                if (empty($value)) $value = "";
            } unset($value);

            log_message('info', '[READ USER DATA] User data successfully retrieved.');
            log_message('info', "[READ USER DATA] Returned JSON Body: " . json_encode($userData, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END READ USER DATA] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($userData);

        } catch (\Throwable $e) {
            log_message('error', '[READ USER DATA] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END READ USER DATA] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|******************************|
    #|* User Insert / Registration *|
    #|******************************|
    public function user_registration() {
        try {
            log_message('info', "\n\n====== [USER REGISTRATION] ======\n");

            # DB Init for transaction control
            $db = \Config\Database::connect();

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[USER REGISTRATION] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'name',
                'username',
                'password',
                'email',
                'phone',
                'experience',
                'education',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            $newData = [
                'name'         => normalize_string($data['name']),
                'username'     => normalize_username($data['username']),
                'password'     => $data['password'] ?? null,
                'phone'        => format_phone_number($data['phone']) ?? null,
                'email'        => normalize_email($data['email']),
                'experience'   => esc(trim($data['experience'])) ?? null,
                'education'    => esc(trim($data['education'])) ?? null,
                'account_role' => 'user',
            ];

            # Default return
            $response = [
                'message' => 'Validation error.' // 422 default
            ];
            $statusCode = 422;

            # CI Validation
            $errors = [];
            $validation = \Config\Services::validation();
            $validation->reset();
            $validation->setRules($this->userRegistrationRules);
            $validated = $validation->run($newData);
            if (!$validated) {
                $errors = $validation->getErrors();
                $response['code'] = 'UNPROCESSABLE';
                $response['details'] = array();

                # Return errors
                foreach($errors as $key => $value) {
                    $response['details'][] = [
                        'field' => $key,
                        'error' => $value
                    ];
                }

                # Specific errors
                if (isset($errors['username']) && stripos($errors['username'], 'already taken') !== false) {
                    $response['message'] = 'Username already exists.';
                    $statusCode = 409;
                }

                log_message('error', '[USER REGISTRATION FORM] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END USER REGISTRATION] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }
        
            $userModel = new UserModel();

            $newData['password'] = password_hash($newData['password'], PASSWORD_BCRYPT);

            $userModel = new UserModel();

            $db->transBegin();
            $user_id = $userModel->insertUser($newData, $db);
            $db->transCommit();

            log_message('info', "[USER REGISTRATION] Account #$user_id created successfully. ");

            return $this->setResponse(201, "Created.");

        } catch (\Throwable $e) {
            log_message('error', '[USER REGISTRATION] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END USER REGISTRATION] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();

            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* User Update / Edit        *|
    #|*****************************|
    public function user_edit($user_id = null) {
        try {
            log_message('info', "\n\n====== [UPDATE USER DATA] ======\n");

            # DB Connection
            $db = \Config\Database::connect();

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[UPDATE USER DATA] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[UPDATE USER DATA] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[UPDATE USER DATA] No token provided.');
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[UPDATE USER DATA] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[UPDATE USER DATA] Invalid JWT format.');
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[UPDATE USER DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[UPDATE USER DATA] Invalid token structure.');
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[UPDATE USER DATA] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[UPDATE USER DATA] Access denied. Role is not "user".');
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[UPDATE USER DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[UPDATE USER DATA] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'name',
                'password',
                'email',
                'phone',
                'experience',
                'education',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            # Prepare received form data
            $newData = [
                'name'       => normalize_string($data['name']),
                'phone'      => format_phone_number($data['phone']),
                'email'      => normalize_email($data['email']),
                'password'   => $data['password'] ?? null,
                'experience' => esc(trim($data['experience'])) ?? null,
                'education'  => esc(trim($data['education'])) ?? null,
            ];

            # Default return
            $response = [
                'message'    => 'Validation error.'
            ];
            $statusCode = 422;

            # CI Validation
            $validation = \Config\Services::validation();
            $validation->reset();
            $validation->setRules($this->userUpdatenRules);
            $validated = $validation->run($newData);

            if (!$validated) {
                $errors = $validation->getErrors();
                $response['code'] = 'UNPROCESSABLE';
                $response['details'] = array();

                # Return errors
                foreach($errors as $key => $value) {
                    $response['details'][] = [
                        'field' => $key,
                        'error' => $value
                    ];
                }

                log_message('error', '[UPDATE USER DATA] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }

            # Deal with password sepparetely (if empty ? keeps current : change to new)
            if (!empty($data['password'])) {
                $newData['password'] = password_hash($newData['password'], PASSWORD_BCRYPT);
            } else {
                unset($newData['password']);
            }

            $userModel = new UserModel();

            # Verify if the current user exists
            $userInfo = $userModel->getUserDataByID($user_id);

            if (empty($userInfo)) {
                log_message('warning', '[UPDATE USER DATA] User not found. ID: ' . $user_id);
                log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

                return $this->setResponse(403, "User not found.");
            }

            $db->transBegin();
            $userModel->updateUser($user_id, $newData, $db);
            $db->transCommit();

            log_message('info', "[UPDATE USER DATA] Account #$user_id updated successfully. ");
            log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

            return $this->setResponse(200, "Profile updated successfully.");

        } catch (\Throwable $e) {
            log_message('error', '[UPDATE USER DATA] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END UPDATE USER DATA] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();
            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* User Delete               *|
    #|*****************************|
    public function user_delete($user_id = null) {
        try {
            log_message('info', "\n\n====== [USER DELETE] ======\n");

            $db = \Config\Database::connect();

            # Validate ID
            if (empty($user_id)) {
                log_message('warning', '[USER DELETE] Empty user_id.');
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(403, "User not found.");
            }

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[USER DELETE] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[USER DELETE] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[USER DELETE] No token provided.');
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[USER DELETE] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[USER DELETE] Invalid JWT format.');
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[USER DELETE] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[USER DELETE] Invalid token structure.');
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[USER DELETE] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[USER DELETE] Access denied. Role is not "user".');
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[USER DELETE] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END USER DELETE] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $userModel = new UserModel();

            $db->transBegin();
            $deleted = $userModel->deleteUser($user_id, $db);
            # Add the token to blacklist
            $blackListModel->add_token($token);
            $db->transCommit();

            log_message('info', "[USER DELETE] Account #$user_id deleted successfully.");
            log_message('info', "\n\n====== [END USER DELETE] ======\n");

            $loggedUsersModel = new LoggedUsersModel();
            $loggedUsersModel
                ->where('jwt_token', $token)
                ->delete();

            return $this->setResponse(200, "User deleted successfully.");

        } catch (\Throwable $e) {
            log_message('error', '[USER DELETE] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END USER DELETE] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();
            if ($db->transStatus() === true) $db->transRollback();

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
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
    
    