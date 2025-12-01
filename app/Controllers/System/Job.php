<?php

namespace App\Controllers\System;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\System\JobModel;
use App\Models\System\JobApplicationModel;
use App\Models\System\BlackListModel;
use App\Models\Companies\CompanyModel;
use App\Models\LoggedUsersModel;
use App\Models\Users\UserModel;

class Job extends BaseController
{
    protected $helpers = ['misc_helper'];

    protected $jobRules = [
        'title' => [
            'rules' => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required' => 'Job Title is required.',
                'min_length' => 'Job Title must be at least 4 characters long.',
                'max_length' => 'Job Title must not exceed 150 characters.',
            ],
        ],
        'area' => [
            'rules' => 'required|in_list[Administração,Agricultura,Artes,Atendimento ao Cliente,Comercial,Comunicação,Construção Civil,Consultoria,Contabilidade,Design,Educação,Engenharia,Finanças,Jurídica,Logística,Marketing,Produção,Recursos Humanos,Saúde,Segurança,Tecnologia da Informação,Telemarketing,Vendas,Outros]',
            'errors' => [
                'required' => 'Job Area is required.',
            ],
        ],
        'description' => [
            'rules' => 'required|min_length[10]|max_length[5000]',
            'errors' => [
                'required' => 'Job Description is required.',
                'min_length' => 'Job Description must be at least 4 characters long.',
                'max_length' => 'Job Description must not exceed 20 characters.',
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
        'salary' => [
            'rules' => 'permit_empty|numeric',
            'errors' => [
                'numeric' => 'Salary must be a valid number.',
            ],
        ],
    ];

    protected $applicationRules = [
        'name' => [
            'rules' => 'required|min_length[4]|max_length[150]',
            'errors' => [
                'required' => 'Full Name is required.',
                'min_length' => 'Full Name must be at least 4 characters long.',
                'max_length' => 'Full Name must not exceed 150 characters.',
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
            'rules' => 'required|min_length[10]|max_length[600]',
            'errors' => [
                'required' => 'Experience is required.',
                'min_length' => 'Experience cannot be less than 10 characters.',
                'max_length' => 'Experience must not exceed 600 characters.',
            ],
        ],
        'education' => [
            'rules' => 'required|min_length[10]|max_length[600]',
            'errors' => [
                'required' => 'Education is required.',
                'min_length' => 'Education cannot be less than 10 characters.',
                'max_length' => 'Education must not exceed 600 characters.',
            ],
        ],
    ];

    protected $feedbackRules = [
        'feedback' => [
            'rules' => 'required|min_length[10]|max_length[600]',
            'errors' => [
                'required' => 'Feedback is required.',
                'min_length' => 'Feedback cannot be less than 10 characters.',
                'max_length' => 'Feedback must not exceed 600 characters.',
            ],
        ],
    ];

    public $session;

    public function __construct() {
        $this->session = session();
    }

    public function index($role) {
        $availableJobs = array();
        $availableJobs = [
            'jobs' => [
                ['value' => 'Administração', 'display' => 'Administration'],
                ['value' => 'Agricultura', 'display' => 'Agriculture'],
                ['value' => 'Artes', 'display' => 'Arts'],
                ['value' => 'Atendimento ao Cliente', 'display' => 'Customer Service'],
                ['value' => 'Comercial', 'display' => 'Commercial'],
                ['value' => 'Comunicação', 'display' => 'Communication'],
                ['value' => 'Construção Civil', 'display' => 'Construction'],
                ['value' => 'Consultoria', 'display' => 'Consulting'],
                ['value' => 'Contabilidade', 'display' => 'Accounting'],
                ['value' => 'Design', 'display' => 'Design'],
                ['value' => 'Educação', 'display' => 'Education'],
                ['value' => 'Engenharia', 'display' => 'Engineering'],
                ['value' => 'Finanças', 'display' => 'Finance'],
                ['value' => 'Jurídica', 'display' => 'Legal'],
                ['value' => 'Logística', 'display' => 'Logistics'],
                ['value' => 'Marketing', 'display' => 'Marketing'],
                ['value' => 'Produção', 'display' => 'Production'],
                ['value' => 'Recursos Humanos', 'display' => 'Human Resources'],
                ['value' => 'Saúde', 'display' => 'Healthcare'],
                ['value' => 'Segurança', 'display' => 'Security'],
                ['value' => 'Tecnologia da Informação', 'display' => 'Information Technology (I.T)'],
                ['value' => 'Telemarketing', 'display' => 'Telemarketing'],
                ['value' => 'Vendas', 'display' => 'Sales'],
                ['value' => 'Outros', 'display' => 'Others'],
            ]
        ];

        if ($role === "company") return view("company/jobs", $availableJobs);
        else if ($role === "user") return view("user/jobs", $availableJobs);
    }


    #|*****************************|
    #|* List Available Jobs       *|
    #|*****************************|
    public function list_available_jobs() {
        try {
            log_message('info', "\n\n====== [LIST AVAILABLE JOBS] ======\n");

            $db = \Config\Database::connect();

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[LIST AVAILABLE JOBS] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[LIST AVAILABLE JOBS] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[LIST AVAILABLE JOBS] No token provided.');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[LIST AVAILABLE JOBS] Malformed JWT Token.');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[LIST AVAILABLE JOBS] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[LIST AVAILABLE JOBS] Invalid token structure.');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[LIST AVAILABLE JOBS] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[LIST AVAILABLE JOBS] Access denied. Role is not "user".');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[LIST AVAILABLE JOBS] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $filters = $this->normalizeFilters($data);

            // log_message('info', "[LIST AVAILABLE JOBS] Normalized Data: " . json_encode($filters, JSON_PRETTY_PRINT));

            $vars = [
                'company_id'   => null,
                'title'        => normalize_string($filters['title']),
                'area'         => normalize_job_area($filters['area']),
                'company'      => normalize_string($filters['company']),
                'description'  => null,
                'state'        => valid_state($filters['state']),
                'city'         => normalize_string($filters['city']),
                'min'          => $filters['salary_range']['min'],
                'max'          => $filters['salary_range']['max']
            ];

            $jobModel = new JobModel();
            $jobs = $jobModel->getJobs($vars);

            if (empty($jobs)) {
                log_message('warning', '[LIST AVAILABLE JOBS] No jobs found.');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(404, "No jobs found.");
            }

            # Convert fields to expected type
            foreach ($jobs as &$job) {
                $job['job_id'] = (int) $job['job_id'];
                $job['salary'] = $job['salary'] !== null 
                                ? round((float) $job['salary'], 2)
                                : null;
            }
            unset($job);

            $result = ["items" => $jobs];

            log_message('info', "[LIST AVAILABLE JOBS] Returned JSON Body: " . json_encode($result, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($result);

        } catch (\Throwable $e) {
            log_message('error', '[LIST AVAILABLE JOBS] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* List Company Jobs         *|
    #|*****************************|
    public function list_company_jobs($user_id) {
        try {
            log_message('info', "\n\n====== [LIST COMPANY JOBS] ======\n");

            $db = \Config\Database::connect();

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[LIST COMPANY JOBS] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[LIST COMPANY JOBS] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[LIST COMPANY JOBS] No token provided.');
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[LIST COMPANY JOBS] Malformed JWT Token.');
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[LIST COMPANY JOBS] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[LIST COMPANY JOBS] Invalid token structure.');
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[LIST COMPANY JOBS] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[LIST COMPANY JOBS] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate Company ID (company_id === sub)
            if ($sub != $user_id) {
                log_message('warning', "[LIST COMPANY JOBS] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($user_id);

            # Neve triggered, but we'll keep it as a second validation
            if (empty($companyData)) {
                log_message('warning', '[LIST COMPANY JOBS] No company found associated with USER ID: ' . $user_id);
                log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[LIST COMPANY JOBS] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $filters = $this->normalizeFilters($data);

            // log_message('info', "[LIST COMPANY JOBS] Normalized Data: " . json_encode($filters, JSON_PRETTY_PRINT));

            $vars = [
                'company_id'   => $companyData['company_id'],
                'title'        => normalize_string($filters['title']),
                'area'         => normalize_job_area($filters['area']),
                'company'      => null,
                'state'        => valid_state($filters['state']),
                'city'         => normalize_string($filters['city']),
                'min'          => $filters['salary_range']['min'],
                'max'          => $filters['salary_range']['max']
            ];

            $jobModel = new JobModel();
            $jobs = $jobModel->getJobs($vars);

            if (empty($jobs)) {
                log_message('warning', '[LIST AVAILABLE JOBS] No jobs found.');
                log_message('info', "\n\n====== [END LIST AVAILABLE JOBS] ======\n");

                return $this->setResponse(404, "No jobs found.");
            }

            # Convert fields to expected type
            foreach ($jobs as &$job) {
                $job['job_id'] = (int) $job['job_id'];
                $job['salary'] = $job['salary'] !== null 
                                ? round((float) $job['salary'], 2)
                                : null;
            }
            unset($job);

            $result = ["items" => $jobs];

            log_message('info', "[LIST COMPANY JOBS] Returned JSON Body: " . json_encode($result, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($result);

        } catch (\Throwable $e) {
            log_message('error', '[LIST COMPANY JOBS] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END LIST COMPANY JOBS] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* Read Job Details          *|
    #|*****************************|
    public function read_job_details($job_id) {
        try {
            log_message('info', "\n\n====== [READ JOB DETAILS] ======\n");

            if (empty($job_id)) {
                log_message('error', '[READ JOB DETAILS] No Job ID provided.');
                log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            $db = \Config\Database::connect();

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[READ JOB DETAILS] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[READ JOB DETAILS] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[READ JOB DETAILS] No token provided.');
                log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[READ JOB DETAILS] Malformed JWT Token.');
                log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[READ JOB DETAILS] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[READ JOB DETAILS] Invalid token structure.');
                log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[READ JOB DETAILS] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            $jobModel = new JobModel();
            
            if ($decoded['role'] === 'company') {
                $companyModel = new CompanyModel();
                $companyData = $companyModel->getCompanyDataByID($sub);

                # Neve triggered, but we'll keep it as a second validation
                if (empty($companyData)) {
                    log_message('warning', '[READ JOB DETAILS] No company found associated with USER ID: ' . $user_id);
                    log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                    return $this->setResponse(404, "Company not found.");
                }

                $isOwnJob = $jobModel->verifyJobByCompanyID($companyData['company_id'], $job_id);
                if (!$isOwnJob) {
                    log_message('warning', '[READ JOB DETAILS] Trying to access jobID from some other company. ');
                    log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                    return $this->setResponse(403, "Forbidden");
                }
            }

            $jobs = $jobModel->getJobByID($job_id);

            if (empty($jobs)) {
                log_message('warning', '[READ JOB DETAILS] No Jobs found.');
                log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

                return $this->setResponse(404, "No jobs found.");
            }

            # Convert fields to expected type
            $jobs['job_id'] = (int) $jobs['job_id'];
            $jobs['salary'] = $jobs['salary'] !== null ? round((float) $jobs['salary'], 2): null;

            log_message('info', "[READ JOB DETAILS] Returned JSON Body: " . json_encode($jobs, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($jobs);

        } catch (\Throwable $e) {
            log_message('error', '[READ JOB DETAILS] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END READ JOB DETAILS] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*********************************|
    #|* Job Insert / Create           *|
    #|*********************************|
    public function create_job() {
        try {
            log_message('info', "\n\n====== [CREATE JOB] ======\n");

             # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[CREATE JOB] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[CREATE JOB] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[CREATE JOB] No token provided.');
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[CREATE JOB] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[CREATE JOB] Invalid JWT format.');
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[CREATE JOB] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[CREATE JOB] Invalid token structure.');
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[CREATE JOB] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[CREATE JOB] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # DB Init for transaction control
            $db = \Config\Database::connect();

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[CREATE JOB] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'title',
                'area',
                'description',
                'state',
                'city',
                'salary',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($sub);

            if (empty($companyData)) {
                log_message('warning', '[CREATE JOB] No company found associated with USER ID: ' . $user_id);
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            $companyID = $companyData['company_id'];

            $newData = [
                'company_id'   => $companyID,
                'title'        => normalize_string($data['title']),
                'area'         => normalize_job_area($data['area']),
                'description'  => esc(trim($data['description'])),
                'state'        => valid_state($data['state']),
                'city'         => normalize_string($data['city']),
                'salary'       => doubleval($data['salary']),
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
            $validation->setRules($this->jobRules);
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

                log_message('error', '[CREATE JOB] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END CREATE JOB] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }

            $jobModel = new JobModel();

            $db->transBegin();
            $job_id = $jobModel->insertJob($newData, $db);
            $db->transCommit();

            log_message('info', "[CREATE JOB] Job #$job_id created successfully.");
            log_message('info', "\n\n====== [END CREATE JOB] ======\n");

            return $this->setResponse(201, "Created.");

        } catch(\Throwable $e) {
            log_message('error', '[CREATE JOB] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END CREATE JOB] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();

            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }

    
    #|*****************************|
    #|* Job Update / Edit         *|
    #|*****************************|
    public function edit_job($jobID = null) {
        try {
            log_message('info', "\n\n====== [EDIT JOB] ======\n");

            if (empty($jobID)) {
                log_message('error', "[EDIT JOB] Empty Job ID given.");
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(404, "Job not found.");
            } 

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[EDIT JOB] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[EDIT JOB] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[EDIT JOB] No token provided.');
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[EDIT JOB] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[EDIT JOB] Invalid JWT format.');
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[EDIT JOB] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[EDIT JOB] Invalid token structure.');
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[EDIT JOB] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[EDIT JOB] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # DB Init for transaction control
            $db = \Config\Database::connect();

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[EDIT JOB] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'title',
                'area',
                'description',
                'state',
                'city',
                'salary',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($sub);

            if (empty($companyData)) {
                log_message('warning', '[EDIT JOB] No company found associated with USER ID: ' . $user_id);
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            $companyID = $companyData['company_id'];

            $jobModel = new JobModel();
            $isOwnJob = $jobModel->verifyJobByCompanyID($companyID, $jobID);

            if (!$isOwnJob) {
                log_message('warning', "[EDIT JOB] Job #$jobID does not belong to the current Company.");
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $newData = [
                'company_id'   => $companyID,
                'title'        => normalize_string($data['title']),
                'area'         => normalize_job_area($data['area']),
                'description'  => esc(trim($data['description'])),
                'state'        => valid_state($data['state']),
                'city'         => normalize_string($data['city']),
                'salary'       => doubleval($data['salary']),
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
            $validation->setRules($this->jobRules);
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

                log_message('error', '[EDIT JOB] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END EDIT JOB] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }

            $db->transBegin();
            $jobModel->updateJob($jobID, $newData, $db);
            $db->transCommit();

            log_message('info', "[EDIT JOB] Job #$jobID updated successfully.");
            log_message('info', "\n\n====== [END EDIT JOB] ======\n");

            return $this->setResponse(200, "Job updated successfully.");

        } catch(\Throwable $e) {
            log_message('error', '[EDIT JOB] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END EDIT JOB] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();

            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* Job Delete                *|
    #|*****************************|
    public function delete_job($jobID = null) {
        try {
            log_message('info', "\n\n====== [DELETE JOB] ======\n");

            if (empty($jobID)) {
                log_message('error', "[DELETE JOB] Empty Job ID given.");
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(404, "Job not found.");
            } 

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[DELETE JOB] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[DELETE JOB] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[DELETE JOB] No token provided.');
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(401, "Missing or invalid Authorization Token.");
            }

            log_message('debug', '[DELETE JOB] Full token before decode: ' . $token);

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[DELETE JOB] Invalid JWT format.');
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[DELETE JOB] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[DELETE JOB] Invalid token structure.');
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[DELETE JOB] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[DELETE JOB] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # DB Init for transaction control
            $db = \Config\Database::connect();

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($sub);

            if (empty($companyData)) {
                log_message('warning', '[DELETE JOB] No company found associated with USER ID: ' . $user_id);
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            $companyID = $companyData['company_id'];

            $jobModel = new JobModel();
            $isOwnJob = $jobModel->verifyJobByCompanyID($companyID, $jobID);

            if (!$isOwnJob) {
                log_message('warning', "[DELETE JOB] Job #$jobID does not belong to the current Company.");
                log_message('info', "\n\n====== [END DELETE JOB] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $db->transBegin();
            $jobModel->deleteJob($jobID, $db);
            $db->transCommit();

            log_message('info', "[DELETE JOB] Job #$jobID deleted successfully.");
            log_message('info', "\n\n====== [END DELETE JOB] ======\n");

            return $this->setResponse(200, "Job deleted successfully.");

        } catch(\Throwable $e) {
            log_message('error', '[DELETE JOB] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END DELETE JOB] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();

            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* Apply to Job -> User      *|
    #|*****************************|
    public function apply_to_job($job_id) {
        try {
            log_message('info', "\n\n====== [APPLY TO JOB] ======\n");

            if (empty($job_id)) {
                log_message('error', '[APPLY TO JOB] No Job ID provided.');
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[APPLY TO JOB] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[APPLY TO JOB] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[APPLY TO JOB] No token provided.');
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[APPLY TO JOB] Malformed JWT Token.');
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[APPLY TO JOB] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[APPLY TO JOB] Invalid token structure.');
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[APPLY TO JOB] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[APPLY TO JOB] Access denied. Role is not "user".');
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Verify if the current user has already applied to the job.
            $jobApplicationModel = new JobApplicationModel();

            $hasAlreadyApplied = $jobApplicationModel->verifyApplication($job_id, $sub);
            if ($hasAlreadyApplied) {
                log_message('warning', "[APPLY TO JOB] User already applied to job #$job_id.");
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(200, "You have already applied to this job.");
            }

            # DB Init for transaction control
            $db = \Config\Database::connect();

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[APPLY TO JOB] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'name',
                'email',
                'phone',
                'education',
                'experience',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            $userModel = new UserModel();
            $userData = $userModel->getUserDataByID($sub);

            if (empty($userData)) {
                log_message('warning', '[APPLY TO JOB] No user found associated with USER ID: ' . $sub);
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(404, "User not found.");
            }

            $jobModel = new JobModel();
            $jobData = $jobModel->getJobByID($job_id);

            if (empty($jobData)) {
                log_message('warning', '[APPLY TO JOB] Job not found: ' . $sub);
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            $newData = [
                'name'         => normalize_string($data['name']),
                'email'        => normalize_email($data['email']) ?? null,
                'phone'        => format_phone_number($data['phone']) ?? null,
                'education'    => esc(trim($data['education'])) ?? null,
                'experience'   => esc(trim($data['experience'])) ?? null,
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
            $validation->setRules($this->applicationRules);
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

                log_message('error', '[APPLY TO JOB] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }

            $finalApplication = [
                'job_id'          => $job_id,
                'user_id'         => $sub,
                'user_name'       => $newData['name'],
                'user_email'      => $newData['email'],
                'user_phone'      => $newData['phone'],
                'user_education'  => $newData['education'],
                'user_experience' => $newData['experience'],
                'feedback'        => null 
            ];

            $db->transBegin();
            $jobApplicationModel->insertApplication($finalApplication, $db);
            $db->transCommit();

            log_message('info', "[APPLY TO JOB] Applied to job #$job_id successfully.");
            log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

            return $this->setResponse(200, "Applied to job #$job_id successfully.");

        } catch(\Throwable $e) {
            log_message('error', '[APPLY TO JOB] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END APPLY TO JOB] ======\n");

            if (!isset($db)) $db = \Config\Database::connect();

            if ($db->transStatus() === true) $db->transRollback();

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|******************************|
    #|* List User Job Applications *|
    #|******************************|
    public function list_applications($user_id) {
        try {
            log_message('info', "\n\n====== [LIST USER APPLICATIONS] ======\n");

            if (empty($user_id)) {
                log_message('error', '[LIST USER APPLICATIONS] No User ID provided.');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(404, "User not found.");
            }

            $db = \Config\Database::connect();

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[LIST USER APPLICATIONS] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[LIST USER APPLICATIONS] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[LIST USER APPLICATIONS] No token provided.');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[LIST USER APPLICATIONS] Malformed JWT Token.');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[LIST USER APPLICATIONS] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[LIST USER APPLICATIONS] Invalid token structure.');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[LIST USER APPLICATIONS] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'user'
            if (strtolower($decoded['role'] ?? '') != 'user') {
                log_message('warning', '[LIST USER APPLICATIONS] Access denied. Role is not "user".');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate User ID (user_id === sub)
            if ($sub != $user_id) {
                log_message('warning', "[LIST USER APPLICATIONS] Access denied. User JWT Sub doesn't match URL user_id.");
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $jobApplicationModel = new JobApplicationModel();
            $jobs = $jobApplicationModel->getAppliedJobsByUserID($user_id);

            if (empty($jobs)) {
                log_message('warning', '[LIST USER APPLICATIONS] No jobs found.');
                log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

                return $this->setResponse(404, "No jobs found.");
            }

            # Convert fields to expected type
            foreach ($jobs as &$job) {
                $job['job_id'] = (int) $job['job_id'];
                $job['salary'] = $job['salary'] !== null 
                                ? round((float) $job['salary'], 2)
                                : null;
            }
            unset($job);

            $result = ["items" => $jobs];

            log_message('info', "[LIST USER APPLICATIONS] Returned JSON Body: " . json_encode($result, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($result);

        } catch (\Throwable $e) {
            log_message('error', '[LIST USER APPLICATIONS] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END LIST USER APPLICATIONS] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*******************************|
    #|* List Company Job Candidates *|
    #|*******************************|
    public function list_candidates($company_id, $job_id) {
        try {
            log_message('info', "\n\n====== [LIST CANDIDATES] ======\n");

            if (empty($company_id)) {
                log_message('error', '[LIST CANDIDATES] No Company ID provided.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            if (empty($job_id)) {
                log_message('error', '[LIST CANDIDATES] No Job ID provided.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            $db = \Config\Database::connect();

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[LIST CANDIDATES] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[LIST CANDIDATES] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[LIST CANDIDATES] No token provided.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[LIST CANDIDATES] Malformed JWT Token.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[LIST CANDIDATES] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[LIST CANDIDATES] Invalid token structure.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[LIST CANDIDATES] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[LIST CANDIDATES] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # Validate Company ID (company_id === sub)
            if ($sub != $company_id) {
                log_message('warning', "[LIST CANDIDATES] Access denied. User JWT Sub doesn't match URL company_id.");
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($company_id);
            
            if (empty($companyData)) {
                log_message('warning', "[LIST CANDIDATES] No company found with User ID #$sub.");
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            $jobModel = new JobModel();
            $jobExists = $jobModel->verifyJobByCompanyID($companyData['company_id'], $job_id);

            if (!$jobExists) {
                log_message('warning', "[LIST CANDIDATES] No jobs with id #$job_id found for company #$company_id.");
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            $jobApplicationModel = new JobApplicationModel();
            $candidates = $jobApplicationModel->getCandidatesByJobID($companyData['company_id'], $job_id);

            if (empty($candidates)) {
                log_message('warning', '[LIST CANDIDATES] No candidates found.');
                log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

                return $this->setResponse(404, "No candidates found.");
            }

            # Convert fields to expected type
            foreach ($candidates as &$candidate) {
                $candidate['user_id'] = (int) $candidate['user_id'];
            } unset($candidate);

            $result = ["items" => $candidates];

            log_message('info', "[LIST CANDIDATES] Returned JSON Body: " . json_encode($result, JSON_PRETTY_PRINT));
            log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

            return $this->response->setStatusCode(200)->setContentType("application/json")->setJSON($result);

        } catch (\Throwable $e) {
            log_message('error', '[LIST CANDIDATES] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END LIST CANDIDATES] ======\n");

            return $this->setResponse(500, "Internal Server Error.");
        }
    }


    #|*****************************|
    #|* Send Feedback             *|
    #|*****************************|
    public function send_feedback($job_id) {
        try {
            log_message('info', "\n\n====== [SEND FEEDBACK] ======\n");

            if (empty($job_id)) {
                log_message('error', '[SEND FEEDBACK] No Job ID provided.');
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            # Capture Authorization Header
            $authHeader = $this->request->getHeaderLine('Authorization');
            log_message('info', '[SEND FEEDBACK] Received Authorization Header: ' . ($authHeader ?: 'NONE'));

            $token = null;

            # Extract Bearer token
            if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                log_message('info', '[SEND FEEDBACK] Token extracted from Authorization header.');
            }

            if (empty($token)) {
                log_message('error', '[SEND FEEDBACK] No token provided.');
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(401, "Missing or invalid authorization token.");
            }

            helper('jwt_helper');
            $blackListModel = new BlackListModel();

            # Validate basic JWT format
            if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
                log_message('error', '[SEND FEEDBACK] Malformed JWT Token.');
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(400, "Malformed JWT Token.");
            }

            # Decode token
            $decoded = jwt_decode($token);
            log_message('info', '[SEND FEEDBACK] Decoded token: ' . json_encode($decoded));

            if (empty($decoded) || !isset($decoded['sub'])) {
                log_message('warning', '[SEND FEEDBACK] Invalid token structure.');
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(401, "Invalid or missing Token Payload.");
            }

            # Check blacklist
            $isValidToken = $blackListModel->verify_token($token);
            if (!$isValidToken) {
                log_message('warning', '[SEND FEEDBACK] Token is blacklisted or invalid.');
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(401, "Invalid Token.");
            }

            # Validate role: must be 'company'
            if (strtolower($decoded['role'] ?? '') != 'company') {
                log_message('warning', '[SEND FEEDBACK] Access denied. Role is not "company".');
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            # Retrieve user data
            $sub = $decoded['sub'];

            # DB Init for transaction control
            $db = \Config\Database::connect();

            # Try to get JSON
            $data = $this->request->getJSON(true);

            # If it came empty, try body as array
            if (empty($data)) {
                $raw = $this->request->getBody();
                $data = json_decode($raw, true) ?? [];
            }

            log_message('info', "[SEND FEEDBACK] Received Data: " . json_encode($data, JSON_PRETTY_PRINT));

            $expectedFields = [
                'user_id',
                'message',
            ];

            # Loop through expected fields to identify unexisting indexes.
            foreach ($expectedFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null; // Create Index with null value;
                }
            }

            $companyModel = new CompanyModel();
            $companyData = $companyModel->getCompanyDataByID($sub);

            if (empty($companyData)) {
                log_message('warning', '[SEND FEEDBACK] No company found associated with USER ID: ' . $sub);
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(404, "Company not found.");
            }

            $jobModel = new JobModel();
            $jobData = $jobModel->getJobByID($job_id);

            if (empty($jobData)) {
                log_message('warning', '[SEND FEEDBACK] Job not found: ' . $sub);
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(404, "Job not found.");
            }

            $isOwnJob = $jobModel->verifyJobByCompanyID($companyData['company_id'], $job_id);
            if (!$isOwnJob) {
                log_message('warning', "[SEND FEEDBACK] Job does not belong to Company from user_id #$sub.");
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(403, "Forbidden.");
            }

            $newData = [
                'user_id'   => $data['user_id'],
                'feedback'  => esc($data['message'])
            ];

            # Verify if the company has sent any feedback to user
            $jobApplicationModel = new JobApplicationModel();
            $hasSentFeedback = $jobApplicationModel->verifyFeedback($job_id, $newData['user_id']);
            if ($hasSentFeedback) {
                log_message('warning', "[SEND FEEDBACK] A feedback has been already sent to this user.");
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(200, "A feedback has been already sent to this user.");
            }

            $isValidUserID = $jobModel->checkUserIDFromJob($companyData['company_id'], $newData['user_id'], $job_id);
            if (!$isValidUserID) {
                log_message('warning', "[SEND FEEDBACK] Invalid user ID.");
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->setResponse(200, "User not found.");
            }

            # Default return
            $response = [
                'message' => 'Validation error.' // 422 default
            ];
            $statusCode = 422;

            # CI Validation
            $errors = [];
            $validation = \Config\Services::validation();
            $validation->reset();
            $validation->setRules($this->feedbackRules);
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

                log_message('error', '[SEND FEEDBACK] Invalid form data given: ' . json_encode($response, JSON_PRETTY_PRINT));
                log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

                return $this->response->setStatusCode($statusCode)->setContentType('application/json')->setJSON($response);
            }

            $db->transBegin();
            $jobApplicationModel->updateFeedback($job_id, $newData['user_id'], $newData['feedback'], $db);
            $db->transCommit();

            log_message('info', "[SEND FEEDBACK] Feedback sent successfully.");
            log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

            return $this->setResponse(200, "Feedback sent successfully.");

        } catch(\Throwable $e) {
            log_message('error', '[SEND FEEDBACK] Error: ' . $e->getMessage());
            log_message('info', "\n\n====== [END SEND FEEDBACK] ======\n");

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


    /**
     * Normalize received job filters
     */
    private function normalizeFilters(array $data): array {
        // If "filters" does not exist, is null, invalid string or isn't an array, force an empty struct.
        if (!isset($data['filters']) || !is_array($data['filters'])) {
            $data['filters'] = [];
        }

        // Expected filters as array with 1 element (as default), but accept any format
        $filters = $data['filters'][0] ?? [];

        // Expected fields
        $expected = [
            'title'         => "",
            'area'          => "",
            'company'       => "",
            'state'         => "",
            'city'          => "",
            'salary_range'  => null,
        ];

        // Create non-received indexes
        foreach ($expected as $key => $default) {
            if (!array_key_exists($key, $filters)) {
                $filters[$key] = $default;
            }
        }

        // Normalize salary_range
        $filters['salary_range'] = $this->normalizeSalaryRange(
            $filters['salary_range']
        );

        return $filters;
    }



    /**
     * Normalize salary_range in every scenario possible (I hope so...)
     * 
     * Rules:
     * - salary_range: null -> returns ['min'=>null,'max'=>null]
     * - any missing field -> create index as null value
     * - 0 values -> convert to null
     * - min > max -> switch values
     * - empty strings -> null
     * - salary_range non-array → ensures array
     */
    private function normalizeSalaryRange($range): array {
        // If is null, empty, string or non-array, ensure default struct
        if (!is_array($range)) {
            return [
                'min' => null,
                'max' => null,
            ];
        }

        // Ensure that min and max exist
        $min = $range['min'] ?? null;
        $max = $range['max'] ?? null;

        // Convert empty string or 0:
        $min = (isset($min) && $min != 0) ? (float)$min : null;
        $max = (isset($max) && $max != 0) ? (float)$max : null;

        // If both null, return default.
        if ($min === null && $max === null) {
            return [
                'min' => null,
                'max' => null,
            ];
        }

        // If min > max -> switch
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [
            'min' => $min,
            'max' => $max,
        ];
    }
}
    
    