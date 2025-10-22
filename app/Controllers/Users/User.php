<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\System\BlackListModel;
use App\Models\Users\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    protected $helpers = ['misc_helper'];

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


    public $session;


    public function __construct() {
        $this->session = session();
    }


    public function index() {
        return view('system/user_profile');
    }


    # Read User Data
    public function read_user_data($user_id = null) {
        log_message('info', '[READ USER DATA] ===== Starting READ USER DATA process =====');

        # Validate HTTP method
        if (!$this->request->is('get')) {
            log_message('warning', '[READ USER DATA] Invalid request method: ' . $this->request->getMethod());
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

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
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        // log_message('debug', '[READ USER DATA] Full token before decode: ' . $token);

        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[READ USER DATA] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[READ USER DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[READ USER DATA] Invalid token structure.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[READ USER DATA] Token is blacklisted or invalid.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[READ USER DATA] Access denied. Role is not "user".');
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate role: must be 'user'
            if ($sub != $user_id) {
                log_message('warning', "[READ USER DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            $userModel = new UserModel();
            $userData = $userModel->getUserDataByID($user_id);

            if (empty($userData)) {
                log_message('warning', '[READ USER DATA] User not found. ID: ' . $user_id);
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'User not found.'
                ]);
            }

            # Remove sensitive data
            unset($userData['password']);
            unset($userData['user_id']);

            foreach ($userData as $col => $value) {
                if (empty($value)) $value = "";
            }

            log_message('info', '[READ USER DATA] User data successfully retrieved.');
            // log_message('debug', '[READ USER DATA] User Data: ' . json_encode($userData));

            return $this->response->setStatusCode(200)->setJSON($userData);

        } catch (\Throwable $e) {
            log_message('error', '[READ USER DATA ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }


    # User Insertion / Registration
    public function user_registration() {

        if(!$this->request->is('POST')) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }
    
        $json = $this->request->getBody();
        if (!empty($json)) {
            try {
                $data = $this->request->getJSON(true);
            } catch (\Exception $e) {
                $data = [];
            }
        }

        if (empty($data)) {
            $data = $this->request->getPost();
        }

        log_message('info', json_encode($data, JSON_PRETTY_PRINT));

        $newData = [
            'name'         => esc(normalize_string($data['name'])),
            'username'     => esc(mb_strtolower(normalize_string($data['username']))),
            'password'     => esc($data['password']) ?? "",
            'phone'        => isset($data['phone']) ? esc(trim(format_phone_number($data['phone']))) : "",
            'email'        => isset($data['email']) ? esc(mb_strtolower(normalize_string($data['email']))) : "",
            'experience'   => isset($data['experience']) ? esc($data['experience']) : "",
            'education'    => isset($data['education']) ? esc($data['education']) : "",
            'account_role' => 'user',
        ];

        # Default return
        $response = [
            'message' => 'Validation error' // 422 default
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

            log_message('error', '[USER REGISTRATION FORM] Invalid form data given:');
            # Return errors
            foreach($errors as $key => $value) {
                log_message('info', $key . ': ' . $value);
                $response['details'][] = [
                    'field' => $key,
                    'error' => $value
                ];
            }

            log_message('error', '[USER REGISTRATION FORM] END;');

            # Specific errors
            if (isset($errors['username']) && stripos($errors['username'], 'already taken') !== false) {
                $response['message'] = 'Username already exists.';
                $statusCode = 409;
            }

            return $this->response->setStatusCode($statusCode)->setJSON($response);
        }
    
        $userModel = new UserModel();

        $newData['password'] = password_hash($newData['password'], PASSWORD_BCRYPT);

        $userModel = new UserModel();

        $userID = $userModel->insertUser($newData);
        if (!$userID) {
            log_message('error', '[USER REGISTRATION FORM] Failed to insert user data.');
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Server Internal Error.'
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'message' => 'Created.'
        ]);
    }


    # User Update / Edit
    public function user_edit($user_id = null) {
        log_message('info', '[UPDATE USER DATA] ===== Starting UPDATE USER DATA process =====');

        # Validate HTTP method
        if (!$this->request->is('patch')) {
            log_message('warning', '[UPDATE USER DATA] Invalid request method: ' . $this->request->getMethod());
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

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
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        log_message('debug', '[UPDATE USER DATA] Full token before decode: ' . $token);

        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[UPDATE USER DATA] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[UPDATE USER DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[UPDATE USER DATA] Invalid token structure.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[UPDATE USER DATA] Token is blacklisted or invalid.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[UPDATE USER DATA] Access denied. Role is not "user".');
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[UPDATE USER DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            $json = $this->request->getBody();
            if (!empty($json)) {
                try {
                    $data = $this->request->getJSON(true);
                } catch (\Exception $e) {
                    $data = [];
                }
            }
            if (empty($data)) {
                $data = $this->request->getPost();
            }

            # Prepare received form data
            $newData = [
                'name'       => isset($data['name']) ? esc(normalize_string($data['name'])) : "",
                'phone'      => isset($data['phone']) ? esc(trim(format_phone_number($data['phone']))) : "",
                'email'      => isset($data['email']) ? esc(mb_strtolower(normalize_string($data['email']))) : "",
                'experience' => isset($data['experience']) ? esc($data['experience']) : "",
                'education'  => isset($data['education']) ? esc($data['education']) : "",
                'password'   => isset($data['password']) ? $data['password'] : ""
            ];

            # Default return
            $response = [
                'message'    => 'Validation error'
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

                log_message('error', '[USER REGISTRATION FORM] Invalid form data given:');
                # Return errors
                foreach($errors as $key => $value) {
                    log_message('info', $key . ': ' . $value);
                    $response['details'][] = [
                        'field' => $key,
                        'error' => $value
                    ];
                }

                log_message('error', '[USER REGISTRATION FORM] END;');
                return $this->response->setStatusCode($statusCode)->setJSON($response);
            }

            # Deal with password sepparetely (if empty ? keeps current : change to new)
            if (!empty($data['password'])) {
                $newData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            } else {
                unset($newData['password']);
            }

            $userModel = new UserModel();

            # Verify if the current user exists
            $userInfo = $userModel->getUserDataByID($user_id);

            if (empty($userInfo)) {
                log_message('warning', '[UPDATE USER DATA] User not found. ID: ' . $user_id);
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'User not found.'
                ]);
            }

            $updated = $userModel->updateUser($user_id, $newData);

            if (!$updated) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Failed to update user data.'
                ]);
            }

            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'Profile updated successfully.'
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[UPDATE USER DATA ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }


    # User DELETE
    public function user_delete($user_id = null) {
        log_message('info', '[DELETE USER] ===== Starting DELETE USER process =====');

        # Validate HTTP method
        if (!$this->request->is('delete')) {
            log_message('warning', '[DELETE USER] Invalid request method: ' . $this->request->getMethod());
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

        # Capture Authorization Header
        $authHeader = $this->request->getHeaderLine('Authorization');
        log_message('info', '[DELETE USER] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

        $token = null;

        # Extract Bearer token
        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            log_message('info', '[DELETE USER] Token extracted from Authorization header.');
        }

        if (empty($token)) {
            log_message('error', '[DELETE USER] No token provided.');
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        log_message('debug', '[DELETE USER] Full token before decode: ' . $token);

        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[DELETE USER] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[DELETE USER] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[DELETE USER] Invalid token structure.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[DELETE USER] Token is blacklisted or invalid.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[DELETE USER] Access denied. Role is not "user".');
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[DELETE USER] Access denied. User JWT Sub doesn't match URL user_id.");
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            $userModel = new UserModel();
            $deleted = $userModel->deleteUser($user_id);

            # Add the token to blacklist
            $inserted = $blackListModel->add_token($token);
            if (!$inserted) {
                log_message('error', '[LOGOUT] Failed to insert token into blacklist.');
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Internal Server Error.'
                ]);
            }

            if (!$deleted) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Internal Server Error.'
                ]);
            }

            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[DELETE USER ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }

}
    
    