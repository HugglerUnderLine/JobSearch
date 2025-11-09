<?php

namespace App\Controllers\Companies;

use App\Controllers\BaseController;
use App\Controllers\Users\User;
use App\Models\Companies\CompanyModel;
use App\Models\System\BlackListModel;
use App\Models\Users\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Company extends BaseController
{
    protected $helpers = ['misc_helper'];

    protected $companyRegistrationRules = [
        'name' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Company name is required.',
                'min_length' => 'Company name must be at least 4 characters long.',
                'max_length' => 'Company name must not exceed 150 characters.',
            ],
        ],
        'business' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Business is required.',
                'min_length' => 'Business must be at least 4 characters long.',
                'max_length' => 'Business must not exceed 150 characters.',
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
        'street' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Street is required.',
                'min_length' => 'Street must be at least 4 characters long.',
                'max_length' => 'Street must not exceed 150 characters.',
            ],
        ],
        'number' => [
            'rules' => 'required|is_natural_no_zero|min_length[1]|max_length[8]',
            'errors' => [
                'required' => 'Number is required.',
                'is_natural_no_zero' => 'Number must contain a valid (integer) value and cannot be zero.',
                'min_length' => 'Number must be at least 1 digit long.',
                'max_length' => 'Number must not exceed 8 digits.',
            ],
        ],
        'city' => [
            'rules' => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required' => 'City is required.',
                'min_length' => 'City must be at least 3 characters long.',
                'max_length' => 'City must not exceed 150 characters.',
            ],
        ],
        'state' => [
            'rules' => 'required|valid_state',
            'errors' => [
                'required' => 'State is required.',
                'valid_state' => 'Please select a valid state.',
            ],
        ],
        'phone' => [
            'rules' => 'required|min_length[10]|max_length[14]',
            'errors' => [
                'required' => 'Phone number is required.',
                'min_length' => 'Phone number must be at least 10 digits long.',
                'max_length' => 'Phone number must not exceed 14 digits.',
            ],
        ],
        'email' => [
            'rules' => 'required|valid_email|min_length[10]|max_length[150]',
            'errors' => [
                'required' => 'Email is required.',
                'valid_email' => 'Please provide a valid email address.',
                'min_length' => 'Email must be at least 10 characters long.',
                'max_length' => 'Email must not exceed 150 characters.',
            ],
        ],
    ];

    protected $companyUpdateRules = [
        'name' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Company name is required.',
                'min_length' => 'Company name must be at least 4 characters long.',
                'max_length' => 'Company name must not exceed 150 characters.',
            ],
        ],
        'business' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Business is required.',
                'min_length' => 'Business must be at least 4 characters long.',
                'max_length' => 'Business must not exceed 150 characters.',
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
        'street' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Street is required.',
                'min_length' => 'Street must be at least 4 characters long.',
                'max_length' => 'Street must not exceed 150 characters.',
            ],
        ],
        'number' => [
            'rules' => 'required|is_natural_no_zero|min_length[1]|max_length[8]',
            'errors' => [
                'required' => 'Number is required.',
                'is_natural_no_zero' => 'Number must contain a valid (integer) value and cannot be zero.',
                'min_length' => 'Number must be at least 1 digit long.',
                'max_length' => 'Number must not exceed 8 digits.',
            ],
        ],
        'city' => [
            'rules' => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required' => 'City is required.',
                'min_length' => 'City must be at least 3 characters long.',
                'max_length' => 'City must not exceed 150 characters.',
            ],
        ],
        'state' => [
            'rules' => 'required|valid_state',
            'errors' => [
                'required' => 'State is required.',
                'valid_state' => 'Please select a valid state.',
            ],
        ],
        'phone' => [
            'rules' => 'required|min_length[10]|max_length[14]',
            'errors' => [
                'required' => 'Phone number is required.',
                'min_length' => 'Phone number must be at least 10 digits long.',
                'max_length' => 'Phone number must not exceed 14 digits.',
            ],
        ],
        'email' => [
            'rules' => 'required|valid_email|min_length[10]|max_length[150]',
            'errors' => [
                'required' => 'Email is required.',
                'valid_email' => 'Please provide a valid email address.',
                'min_length' => 'Email must be at least 10 characters long.',
                'max_length' => 'Email must not exceed 150 characters.',
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

    # Read Company Data
    public function read_company_data($user_id = null) {
        log_message('info', '[READ COMPANY DATA] ===== Starting READ COMPANY DATA process =====');

        # Validate HTTP method
        if (!$this->request->is('get')) {
            log_message('warning', '[READ COMPANY DATA] Invalid request method: ' . $this->request->getMethod());
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

        # Capture Authorization Header
        $authHeader = $this->request->getHeaderLine('Authorization');
        log_message('info', '[READ COMPANY DATA] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

        $token = null;

        # Extract Bearer token
        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            log_message('info', '[READ COMPANY DATA] Token extracted from Authorization header.');
        }

        if (empty($token)) {
            log_message('error', '[READ COMPANY DATA] No token provided.');
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        // log_message('debug', '[READ COMPANY DATA] Full token before decode: ' . $token);

        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[READ COMPANY DATA] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[READ COMPANY DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[READ COMPANY DATA] Invalid token structure.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[READ COMPANY DATA] Token is blacklisted or invalid.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[READ COMPANY DATA] Access denied. Role is not "company".');
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate role: must be 'company'
            if ($sub != $user_id) {
                log_message('warning', "[READ COMPANY DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($user_id);

            if (empty($companyData)) {
                log_message('warning', '[READ COMPANY DATA] No company found associated with USER ID: ' . $user_id);
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'Company not found.'
                ]);
            }

            # Remove sensitive data
            unset($companyData['password']);
            unset($companyData['user_id']);
            unset($companyData['account_role']);

            foreach ($companyData as $col => $value) {
                if (empty($value)) $value = "";
            }

            log_message('info', '[READ COMPANY DATA] Company data successfully retrieved.');
            // log_message('debug', '[READ COMPANY DATA] Company Data: ' . json_encode($companyData));

            return $this->response->setStatusCode(200)->setJSON($companyData);

        } catch (\Throwable $e) {
            log_message('error', '[READ COMPANY DATA ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }

    # Company Insertion / Registration
    public function company_registration() {

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
            'business'     => esc(normalize_string($data['business'])),
            'username'     => esc(mb_strtolower(normalize_string($data['username']))),
            'password'     => $data['password'],
            'street'       => esc(normalize_string($data['street'])),
            'number'       => esc(trim($data['number'])),
            'city'         => esc(normalize_string($data['city'])),
            'state'        => esc(trim($data['state'])),
            'phone'        => esc(trim(format_phone_number($data['phone']))),
            'email'        => esc(mb_strtolower(normalize_string($data['email']))),
            'account_role' => 'company'
        ];

        $userData = [
            'name'         => $newData['name'],
            'username'     => $newData['username'],
            'password'     => password_hash($newData['password'], PASSWORD_BCRYPT),
            'phone'        => $newData['phone'],
            'email'        => $newData['email'],
            'experience'   => "",
            'education'    => "",
            'account_role' => 'company',
        ];

        $companyData = [
            'business'     => $newData['business'],
            'street'       => $newData['street'],
            'number'       => $newData['number'],
            'city'         => $newData['city'],
            'state'        => valid_state($newData['state']),
        ];

        // log_message('info', json_encode($newData, JSON_PRETTY_PRINT));

        # Default return
        $response = [
            'message' => 'Validation error' // 422 default
        ];
        $statusCode = 422;

        # CI Validation
        $errors = [];
        $validation = \Config\Services::validation();
        $validation->reset();
        $validation->setRules($this->companyRegistrationRules);
        $validated = $validation->run($newData);
        if (!$validated) {
            $errors = $validation->getErrors();
            $response['code'] = 'UNPROCESSABLE';
            $response['details'] = array();

            log_message('error', '[COMPANY REGISTRATION FORM] Invalid form data given:');
            # Return errors
            foreach($errors as $key => $value) {
                log_message('info', $key . ': ' . $value);
                $response['details'][] = [
                    'field' => $key,
                    'error' => $value
                ];
            }

            log_message('error', '[COMPANY REGISTRATION FORM] END;');

            # Specific errors
            if (isset($errors['username']) && stripos($errors['username'], 'already taken') !== false) {
                $response['message'] = 'Username already exists.';
                $statusCode = 409;
            }

            return $this->response->setStatusCode($statusCode)->setJSON($response);
        }
    
        $userModel = new UserModel();

        $existingUser = $userModel->getCompanyUserByName($userData['name']);

        if (!empty($existingUser)) {
            log_message('error', '[COMPANY REGISTRATION FORM] Company Name already exists: ' . $userData['name']);
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Company Name already exists.'
            ]);
        }

        $companyModel = new CompanyModel();

        $newData['password'] = password_hash($newData['password'], PASSWORD_BCRYPT);

        $userModel = new UserModel();

        $userID = $userModel->insertUser($userData);
        if (!$userID) {
            log_message('error', '[COMPANY REGISTRATION FORM] Failed to insert user data.');
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Server Internal Error.'
            ]);
        }

        unset($newData);

        $companyData['user_id'] = $userID;

        $res = $companyModel->insertCompany($companyData);

        if (!$res) {
            log_message('error', '[COMPANY REGISTRATION FORM] Failed to insert company data.');
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Server Internal Error.'
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Created.'
        ]);
    }

    # Company Update / Edit
    public function company_edit($user_id = null) {
        log_message('info', '[UPDATE COMPANY DATA] ===== Starting UPDATE COMPANY DATA process =====');

        # Validate HTTP method
        if (!$this->request->is('patch')) {
            log_message('warning', '[UPDATE COMPANY DATA] Invalid request method: ' . $this->request->getMethod());
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

        # Capture Authorization Header
        $authHeader = $this->request->getHeaderLine('Authorization');
        log_message('info', '[UPDATE COMPANY DATA] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

        $token = null;

        # Extract Bearer token
        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            log_message('info', '[UPDATE COMPANY DATA] Token extracted from Authorization header.');
        }

        if (empty($token)) {
            log_message('error', '[UPDATE COMPANY DATA] No token provided.');
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        log_message('debug', '[UPDATE COMPANY DATA] Full token before decode: ' . $token);

        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[UPDATE COMPANY DATA] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[UPDATE COMPANY DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[UPDATE COMPANY DATA] Invalid token structure.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[UPDATE COMPANY DATA] Token is blacklisted or invalid.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[UPDATE COMPANY DATA] Access denied. Role is not "company".');
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[UPDATE COMPANY DATA] Access denied. User JWT Sub doesn't match URL user_id.");
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

            log_message('info', json_encode($data, JSON_PRETTY_PRINT));

            $newData = [
                'name'         => esc(normalize_string($data['name'])),
                'business'     => esc(normalize_string($data['business'])),
                'password'     => $data['password'],
                'street'       => esc(normalize_string($data['street'])),
                'number'       => esc(trim($data['number'])),
                'city'         => esc(normalize_string($data['city'])),
                'state'        => esc(normalize_string(trim($data['state']))),
                'phone'        => esc(trim(format_phone_number($data['phone']))),
                'email'        => esc(mb_strtolower(normalize_string($data['email']))),
                'account_role' => 'company'
            ];

            $userData = [
                'name'         => $newData['name'],
                'password'     => $newData['password'],
                'phone'        => $newData['phone'],
                'email'        => $newData['email'],
                'experience'   => "",
                'education'    => "",
                'account_role' => 'company',
            ];

            $companyData = [
                'business'     => $newData['business'],
                'street'       => $newData['street'],
                'number'       => $newData['number'],
                'city'         => $newData['city'],
                'state'        => esc(valid_state($newData['state'])),
            ];

            # Default return
            $response = [
                'message'    => 'Validation error'
            ];
            $statusCode = 422;

            # CI Validation
            $validation = \Config\Services::validation();
            $validation->reset();
            $validation->setRules($this->companyUpdateRules);
            $validated = $validation->run($newData);

            if (!$validated) {
                $errors = $validation->getErrors();
                $response['code'] = 'UNPROCESSABLE';
                $response['details'] = array();

                log_message('error', '[COMPANY UPDATE FORM] Invalid form data given:');
                # Return errors
                foreach($errors as $key => $value) {
                    log_message('info', $key . ': ' . $value);
                    $response['details'][] = [
                        'field' => $key,
                        'error' => $value
                    ];
                }

                log_message('info', "==== STARTING UPDATE DEBUG: ERROR MSG ====");

                log_message("info", json_encode($response, JSON_PRETTY_PRINT));

                log_message('error', '[COMPANY UPDATE FORM] END;');
                return $this->response->setStatusCode($statusCode)->setJSON($response);
            }

            # Deal with password sepparetely (if empty ? keeps current : change to new)
            if (!empty($data['password'])) {
                $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT);
            } else {
                unset($userData['password']);
            }

            $companyModel = new CompanyModel();
            $userModel = new UserModel();

            # Verify if the current company exists
            $companyInfo = $companyModel->getCompanyDataByID($user_id);

            if (empty($companyInfo)) {
                log_message('warning', '[UPDATE COMPANY DATA] Company not found. USER ID: ' . $user_id);
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'Company not found.'
                ]);
            }

            if ($companyInfo['name'] !== $newData['name']) {
                $isValidCompanyName = $userModel->getCompanyUserByName($newData['name']);

                if (!empty($isValidCompanyName)) {
                    log_message('warning', '[UPDATE COMPANY DATA] Company Name already exists: ' . $newData['name']);
                    return $this->response->setStatusCode(409)->setJSON([
                        'message' => 'Company name already exists'
                    ]);
                }
            }

            $updated = $userModel->updateUser($user_id, $userData);
            if (!$updated) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Failed to update company data.'
                ]);
            }

            $companyData['user_id'] = $user_id;
            $updated = $companyModel->updateCompany($user_id, $companyData);

            if (!$updated) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Failed to update company data.'
                ]);
            }

            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'Profile updated successfully.'
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[UPDATE COMPANY DATA ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }


    # Company DELETE
    /*
     * Remember to come back to this point later. The company cannot be deleted if there is any active job listing associated with it.
     */
    public function company_delete($user_id = null) {
        log_message('info', '[DELETE COMPANY] ===== Starting DELETE COMPANY process =====');

        # Validate HTTP method
        if (!$this->request->is('delete')) {
            log_message('warning', '[DELETE COMPANY] Invalid request method: ' . $this->request->getMethod());
            return $this->response->setStatusCode(405)->setJSON([
                'message' => 'Invalid request method.'
            ]);
        }

        # Validate ID
        if (!$user_id) {
            log_message('warning', '[DELETE COMPANY] Empty user_id.');
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Company not found.'
            ]);
        }

        # Capture Authorization Header
        $authHeader = $this->request->getHeaderLine('Authorization');
        log_message('info', '[DELETE COMPANY] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

        $token = null;

        # Extract Bearer token
        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            log_message('info', '[DELETE COMPANY] Token extracted from Authorization header.');
        }

        if (empty($token)) {
            log_message('error', '[DELETE COMPANY] No token provided.');
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Missing or invalid authorization token.'
            ]);
        }

        log_message('debug', '[DELETE COMPANY] Full token before decode: ' . $token);

        helper('jwt_helper');
        $blackListModel = new BlackListModel();

        try {
            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[DELETE COMPANY] Invalid JWT format.');
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Malformed JWT token.'
                ]);
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[DELETE COMPANY] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[DELETE COMPANY] Invalid token structure.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid or missing token payload.'
                ]);
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[DELETE COMPANY] Token is blacklisted or invalid.');
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Invalid token.'
                ]);
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[DELETE COMPANY] Access denied. Role is not "company".');
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[DELETE COMPANY] Access denied. User JWT Sub doesn't match URL user_id.");
                return $this->response->setStatusCode(403)->setJSON([
                    'message' => 'Forbidden.'
                ]);
            }

            $userModel = new UserModel();
            $userData = $userModel->getUserDataByID($user_id);

            if (empty($userData)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'Company not found.'
                ]);
            }

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
                'message' => 'Company deleted successfully.'
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[DELETE COMPANY ERROR] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal Server Error.'
            ]);
        }
    }
    
}
    
    