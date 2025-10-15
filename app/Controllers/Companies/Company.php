<?php

namespace App\Controllers\Companies;

use App\Controllers\BaseController;
use App\Models\Companies\CompanyModel;
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
                'required' => 'Business name is required.',
                'min_length' => 'Business name must be at least 4 characters long.',
                'max_length' => 'Business name must not exceed 150 characters.',
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
            'rules' => 'required|integer|min_length[1]|max_length[8]',
            'errors' => [
                'required' => 'Number is required.',
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

    protected $passwordRules = [
        'current_password' => [
            'rules' => 'required|min_length[8]|max_length[256]|no_spaces',
            'errors' => [
                'required' => 'Please enter your current password.',
                'min_length' => 'Your current password must be at least 8 characters.',
                'max_length' => 'Your current password must not exceed 256 characters.',
                'no_spaces' => 'Your current password cannot contain spaces.',
            ]
        ],
        'new_password' => [
            'rules' => 'required|min_length[8]|max_length[256]|differs[current_password]|no_spaces|strong_password',
            'errors' => [
                'required' => 'Please enter your new password.',
                'min_length' => 'Your new password must be at least 8 characters.',
                'max_length' => 'Your new password must not exceed 256 characters.',
                'differs' => 'Your new password cannot be the same as your current password.',
                'no_spaces' => 'Your new password cannot contain spaces.',
                'strong_password' => 'The New Password must contain at least one uppercase, one lowercase, one number, and one special character.'
            ]
        ],
        'password_confirmation' => [
            'rules' => 'required|matches[new_password]|min_length[8]',
            'errors' => [
                'required' => 'Please confirm your new password.',
                'matches' => 'Password and Password Confirmation do not match.',
                'min_length' => 'Your new password must be at least 8 characters.',
                'max_length' => 'Your new password must not exceed 256 characters.',
            ]
        ]
    ];

    public $session;

    public function __construct() {
        $this->session = session();
    }

    public function index() {
        // TODO
    }

    # Company Insertion / Registration
    public function company_registration() {

        if(!$this->request->is('POST')) {
            return $this->response->setStatusCode(500)->setJSON([
                'textStatus'  => 'error',
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

        // log_message('info', json_encode($data, JSON_PRETTY_PRINT));

        $newData = [
            'name'         => esc(normalize_string($data['name'])),
            'business'     => esc(normalize_string($data['business'])),
            'username'     => esc(mb_strtolower(normalize_string($data['username']))),
            'password'     => esc($data['password']),
            'street'       => esc(normalize_string($data['street'])),
            'number'       => esc(trim($data['number'])),
            'city'         => esc(normalize_string($data['city'])),
            'state'        => esc(trim($data['state'])),
            'phone'        => esc(trim(format_phone_number($data['phone']))),
            'email'        => esc(mb_strtolower(normalize_string($data['email']))),
            'account_role' => 'company'
        ];

        // log_message('info', json_encode($newData, JSON_PRETTY_PRINT));

        # Default return
        $response = [
            'textStatus' => 'error',
            'message' => 'One or more fields are incorrect.' // 422 default
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

            log_message('error', '[COMPANY REGISTRATION FORM] Invalid form data given:');
            # Return errors
            foreach($errors as $key => $value) {
                log_message('info', $key . ': ' . $value);
            }
            log_message('error', '[COMPANY REGISTRATION FORM] END;');

            # Specific errors
            if (isset($errors['username']) && stripos($errors['username'], 'already taken') !== false) {
                $response['message'] = 'Username already exists.';
                $statusCode = 409;
            }

            return $this->response->setStatusCode($statusCode)->setJSON($response);
        }
    
        $companyModel = new CompanyModel();

        $newData['password'] = password_hash($newData['password'], PASSWORD_BCRYPT);

        $newUserData = [
            'name'         => $newData['name'],
            'username'     => $newData['username'],
            'password'     => $newData['password'],
            'phone'        => $newData['phone'],
            'email'        => $newData['email'],
            'account_role' => 'COMPANY',
        ];

        $userModel = new UserModel();

        $userID = $userModel->insertUser($newUserData);
        if (!$userID) {
            log_message('error', '[COMPANY REGISTRATION FORM] Failed to insert user data.');
            return $this->response->setStatusCode(500)->setJSON([
                'textStatus'  => 'error',
                'message' => 'Server Internal Error.'
            ]);
        }

        $newCompanyData = [
            'business'     => $newData['business'],
            'street'       => $newData['street'],
            'number'       => $newData['number'],
            'city'         => $newData['city'],
            'state'        => valid_state($newData['state']),
            'user_id'      => $userID,
        ];

        unset($newData);

        $res = $companyModel->insertCompany($newCompanyData);

        if (!$res) {
            log_message('error', '[COMPANY REGISTRATION FORM] Failed to insert company data.');
            return $this->response->setStatusCode(500)->setJSON([
                'textStatus'  => 'error',
                'message' => 'Server Internal Error.'
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'textStatus'  => 'success',
            'message' => 'Created.'
        ]);
    }
    
}
    
    