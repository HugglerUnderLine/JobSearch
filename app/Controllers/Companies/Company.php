<?php

namespace App\Controllers\Companies;

use App\Controllers\BaseController;
use App\Models\Companies\CompanyModel;
use App\Models\System\BlackListModel;
use App\Models\Users\UserModel;
use App\Models\System\JobModel;
use App\Models\LoggedUsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class Company extends BaseController
{
    #|*****************************|
    #|* Helpers                   *|
    #|*****************************|
    protected $helpers = ['misc_helper', 'logged_users'];


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


    #|*****************************************|
    #|* Index -> Return view Profile          *|
    #|*****************************************|
    public function index() {
        return view('system/profile');
    }


    #|*****************************|
    #|* Read Company Data         *|
    #|*****************************|
    public function read_company_data($user_id = null) {
        try {
            log_message('info', "\n\n====== [READ COMPANY DATA] ======\n");

            $db = \Config\Database::connect();

            $loggedUsersModel = new LoggedUsersModel();

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
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                remove_logged_user(123);

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[READ COMPANY DATA] Malformed JWT Token.');
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[READ COMPANY DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[READ COMPANY DATA] Invalid token structure.');
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[READ COMPANY DATA] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[READ COMPANY DATA] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate role: must be 'company'
            if ($sub != $user_id) {
                log_message('warning', "[READ COMPANY DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($user_id);

            if (empty($companyData)) {
                log_message('warning', '[READ COMPANY DATA] No company found associated with USER ID: ' . $user_id);
                log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            # Remove sensitive data
            unset($companyData['password'], $companyData['user_id'], $companyData['account_role'], $companyData['company_id']);

            foreach ($companyData as $col => &$value) {
                if (empty($value)) $value = "";
            } unset($value);

            log_message('info', '[READ COMPANY DATA] Company data successfully retrieved.');
            log_message('info', "[READ COMPANY DATA] Returned JSON Body: " . json_encode($companyData, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($companyData);

        } catch (\Throwable $e) {
            log_message('error', '[READ COMPANY DATA] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END READ COMPANY DATA] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*********************************|
    #|* Company Insert / Registration *|
    #|*********************************|
    public function company_registration() {
        try {
            log_message('info', "\n\n====== [COMPANY REGISTRATION] ======\n");

            # DB Init for transaction control
            $db = \Config\Database::connect();

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[COMPANY REGISTRATION] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'name',
                'business',
                'username',
                'password',
                'street',
                'number',
                'city',
                'state',
                'phone',
                'email',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            $newData = [
                'name'         => normalize_string($data['name']),
                'business'     => normalize_string($data['business']),
                'username'     => normalize_username($data['username']),
                'password'     => $data['password'] ?? null,
                'street'       => normalize_string($data['street']),
                'number'       => esc(trim($data['number'])),
                'city'         => normalize_string($data['city']),
                'state'        => valid_state($data['state']),
                'phone'        => format_phone_number($data['phone']),
                'email'        => normalize_email($data['email']),
                'account_role' => 'company'
            ];

            $userData = [
                'name'         => $newData['name'],
                'username'     => $newData['username'],
                'password'     => password_hash($newData['password'], PASSWORD_BCRYPT),
                'phone'        => $newData['phone'],
                'email'        => $newData['email'],
                'experience'   => null,
                'education'    => null,
                'account_role' => 'company',
            ];

            $companyData = [
                'business'     => $newData['business'],
                'street'       => $newData['street'],
                'number'       => $newData['number'],
                'city'         => $newData['city'],
                'state'        => $newData['state'],
            ];

            // log_message('info', json_encode($newData, JSON_PRETTY_PRINT));

            # Default return
            $response = [
                'message' => 'Validation error.' // 422 default
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

                log_message('error', '[COMPANY REGISTRATION FORM] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END COMPANY REGISTRATION] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }
        
            $userModel = new UserModel();
            $isUniqueCompanyName = $userModel->isUniqueName($userData['name'], "company");

            if (!$isUniqueCompanyName) {
                log_message('error', '[COMPANY REGISTRATION] Company Name already exists: ' . $userData['name']);
                log_message('info', "\n\n====== [END COMPANY REGISTRATION] ======\n");
                return $this->setResponse(409, "Company Name already exists.");
            }

            $companyModel = new CompanyModel();

            $newData['password'] = password_hash($newData['password'], PASSWORD_BCRYPT);

            $userModel = new UserModel();

            $db->transBegin();
            $user_id = $userModel->insertUser($userData, $db);

            unset($newData);

            $companyData['user_id'] = $user_id;

            $companyModel->insertCompany($companyData, $db);

            # Commit Transaction
            $db->transCommit();

            log_message('info', "[COMPANY REGISTRATION] Account #$user_id created successfully.");
            log_message('info', "\n\n====== [END COMPANY REGISTRATION] ======\n");

            return $this->setResponse(201, "Created.");

        } catch(\Throwable $e) {
            log_message('error', '[COMPANY REGISTRATION] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END COMPANY REGISTRATION] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();

            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }

    
    #|*****************************|
    #|* Company Update / Edit     *|
    #|*****************************|
    public function company_edit($user_id = null) {
        try {
            log_message('info', "\n\n====== [UPDATE COMPANY DATA] ======\n");

            # DB Connection
            $db = \Config\Database::connect();

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
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[UPDATE COMPANY DATA] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[UPDATE COMPANY DATA] Invalid JWT format.');
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[UPDATE COMPANY DATA] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[UPDATE COMPANY DATA] Invalid token structure.');
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[UPDATE COMPANY DATA] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[UPDATE COMPANY DATA] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[UPDATE COMPANY DATA] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[UPDATE COMPANY DATA] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $newData = [
                'name'         => esc(normalize_string($data['name'])),
                'business'     => esc(normalize_string($data['business'])),
                'password'     => $data['password'],
                'street'       => esc(normalize_string($data['street'])),
                'number'       => esc(trim($data['number'])),
                'city'         => esc(normalize_string($data['city'])),
                'state'        => esc(normalize_string($data['state'])),
                'phone'        => esc(format_phone_number($data['phone'])),
                'email'        => esc(mb_strtolower(normalize_string($data['email']))),
                'account_role' => 'company'
            ];

            $userData = [
                'name'         => $newData['name'],
                'password'     => $newData['password'],
                'phone'        => $newData['phone'],
                'email'        => $newData['email'],
                'experience'   => null,
                'education'    => null,
                'account_role' => 'company',
            ];

            $companyData = [
                'business'     => $newData['business'],
                'street'       => $newData['street'],
                'number'       => $newData['number'],
                'city'         => $newData['city'],
                'state'        => valid_state($newData['state']),
            ];

            # Default return
            $response = [
                'message'    => 'Validation error.'
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

                # Return errors
                foreach($errors as $key => $value) {
                    $response['details'][] = [
                        'field' => $key,
                        'error' => $value
                    ];
                }

                log_message('error', '[UPDATE COMPANY DATA] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
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
                log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                return $this->setResponse(403, "Company not found.");
            }

            if ($companyInfo['name'] !== $newData['name']) {
                $isUniqueCompanyName = $userModel->isUniqueName($newData['name'], "company");

                if (!$isUniqueCompanyName) {
                    log_message('warning', '[UPDATE COMPANY DATA] Company Name already exists: ' . $newData['name']);
                    log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

                    return $this->setResponse(409, "Company name already exists.");
                }
            }

            # Start Transaction
            $db->transBegin();
            $userModel->updateUser($user_id, $userData, $db);

            $companyData['user_id'] = $user_id;
            $companyData['company_id'] = $companyInfo['company_id'];
            $companyModel->updateCompany($user_id, $companyData, $db);

            # Commit Transaction
            $db->transCommit();

            log_message('info', "[UPDATE COMPANY DATA] Account #$user_id updated successfully. ");
            log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

            return $this->setResponse(200, "Profile updated successfully.");

        } catch(\Throwable $e) {
            log_message('error', '[UPDATE COMPANY DATA] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END UPDATE COMPANY DATA] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();
            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* Company Delete            *|
    #|*****************************|
    public function company_delete($user_id = null) {
        try {
            log_message('info', "\n\n====== [COMPANY DELETE] ======\n");

            $db = \Config\Database::connect();

            # Validate ID
            if (empty($user_id)) {
                log_message('warning', '[COMPANY DELETE] Empty user_id.');
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(403, "Company not found.");
            }

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[COMPANY DELETE] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[COMPANY DELETE] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[COMPANY DELETE] No token provided.');
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[COMPANY DELETE] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[COMPANY DELETE] Invalid JWT format.');
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[COMPANY DELETE] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[COMPANY DELETE] Invalid token structure.');
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValid = $blackListModel->verify_token($token);
            if (!$isValid) {
                log_message('warning', '[COMPANY DELETE] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[COMPANY DELETE] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            if ($user_id != $sub) {
                log_message('warning', "[COMPANY DELETE] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $userModel = new UserModel();
            $userData = $userModel->getUserDataByID($user_id);

            if (empty($userData)) {
                log_message('info', "[COMPANY DELETE] No user found with provided ID.");
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");
                return $this->setResponse(403, "Company not found.");
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($user_id);

            $jobModel = new JobModel();
            $hasActiveJobs = $jobModel->checkExistingJobs($companyData['company_id']);

            if ($hasActiveJobs) {
                log_message('info', "[COMPANY DELETE] Company #$user_id still have active jobs. Delete aborted.");
                log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");
                return $this->setResponse(409, "Unable to delete account with active jobs.");
            }

            $db->transBegin();

            $userModel->deleteUser($user_id, $db);
            # Add the token to blacklist
            $blackListModel->add_token($token);

            $db->transCommit();

            $loggedUsersModel = new LoggedUsersModel();
            $loggedUsersModel
                ->where('jwt_token', $token)
                ->delete();

            log_message('info', "[COMPANY DELETE] Account #$user_id deleted successfully.");
            log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");
            return $this->setResponse(200, "Company deleted successfully.");

        } catch(\Throwable $e) {
            log_message('error', '[COMPANY DELETE] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END COMPANY DELETE] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();
            if ($db->transStatus() === true) $db->transRollback();

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
    
    