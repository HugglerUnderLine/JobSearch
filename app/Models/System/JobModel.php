<?php

namespace App\Models\System;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table            = 'jobs';
    protected $primaryKey       = 'job_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['company_id', 'title', 'area', 'description', 'state', 'city', 'salary'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    #|******************************|
    #|* Get Jobs                   *|
    #|******************************|
    public function getJobs($vars) {
        try {
            $sql_select = "
                SELECT jobs.job_id,
                       jobs.title,
                       jobs.area,
                       users.name as company,
                       jobs.description,
                       jobs.state,
                       jobs.city,
                       jobs.salary::NUMERIC as salary,
                       users.email AS contact
                FROM jobs
                INNER JOIN companies ON jobs.company_id = companies.company_id
                INNER JOIN users ON companies.user_id = users.user_id";

            $sql_orderBy = "\nORDER BY jobs.job_id ASC";

            $found_where = false;
            $where_params = array();

            if (!empty($vars['company_id'])) {
                $where_params[] = "jobs.company_id = :company_id:";
                $found_where = true;
            }
            if (!empty($vars['title'])) {
                $where_params[] = "UPPER(jobs.title) LIKE :title:";
                $vars['title'] = "%" . $vars['title'] . "%";
                $found_where = true;
            }
            if (!empty($vars['area'])) {
                $where_params[] = "jobs.area = :area:";
                $found_where = true;
            }
            if (!empty($vars['company'])) {
                $where_params[] = "UPPER(users.name) LIKE :company:";
                $vars['company'] = "%" . $vars['company'] . "%";
                $found_where = true;
            }
            if (!empty($vars['state'])) {
                $where_params[] = "UPPER(jobs.state) LIKE :state:";
                $vars['state'] = "%" . $vars['state'] . "%";
                $found_where = true;
            }
            if (!empty($vars['city'])) {
                $where_params[] = "UPPER(jobs.city) LIKE :city:";
                $vars['city'] = "%" . $vars['city'] . "%";
                $found_where = true;
            }
            if (!empty($vars['min']) && !empty($vars['max'])) {
                $where_params[] = "jobs.salary >= :min: AND jobs.salary <= :max:";
                $found_where = true;
            } else if (!empty($vars['min'])) {
                $where_params[] = "jobs.salary >= :min:";
                $found_where = true;
            } else if (!empty($vars['max'])) {
                $where_params[] = "jobs.salary <= :max:";
                $found_where = true;
            }
            $sql_where = '';
            if ($found_where)
                $sql_where = "\nWHERE " . implode(' AND ', $where_params);

            $sql_data = $sql_select . $sql_where . $sql_orderBy;

            return $this->query($sql_data, $vars)->getResultArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Get Jobs] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Get Job By ID              *|
    #|******************************|
    public function getJobByID($job_id) {
        try {
            $sql_select = "
                SELECT jobs.job_id,
                       jobs.title,
                       jobs.area,
                       jobs.description,
                       users.name as company,
                       jobs.state,
                       jobs.city,
                       jobs.salary::NUMERIC AS salary,
                       users.email AS contact
                FROM jobs
                INNER JOIN companies ON jobs.company_id = companies.company_id
                INNER JOIN users ON companies.user_id = users.user_id
                WHERE jobs.job_id = :job_id:";

            return $this->query($sql_select, ['job_id' => $job_id])->getRowArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Get Jobs] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|**********************************|
    #|* Verify Job By Company & Job ID *|
    #|**********************************|
    public function verifyJobByCompanyID($company_id, $job_id) {
        try {
            $sql_query = "
                SELECT COUNT(*) AS total
                FROM jobs
                WHERE jobs.job_id = :job_id:
                AND jobs.company_id = :company_id:";

            $count = $this->query($sql_query, ['job_id' => $job_id, 'company_id' => $company_id])->getRowArray();

            return !empty($count['total']) ? true : false;

        } catch (\Throwable $e) {
            log_message('critical', "[Verify Job By Company ID] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|***************************************|
    #|* Verify if a company has active jobs *|
    #|***************************************|
    public function checkExistingJobs($company_id) {
        try {
            $sql_query = "
                SELECT COUNT(*) AS total
                FROM jobs
                WHERE jobs.company_id = :company_id:";

            $count = $this->query($sql_query, ['company_id' => $company_id])->getRowArray();

            return !empty($count['total']) ? true : false;

        } catch (\Throwable $e) {
            log_message('critical', "[Check Existing Jobs] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|*****************************************|
    #|* Check User ID before sending feedback *|
    #|*****************************************|
    public function checkUserIDFromJob($company_id, $user_id, $job_id) {
        try {
            $sql_query = "
                SELECT COUNT(*) AS total
                FROM jobs
                INNER JOIN job_applications ON jobs.job_id = job_applications.job_id
                WHERE jobs.company_id = :company_id:
                AND job_applications.user_id = :user_id:
                AND job_applications.job_id = :job_id:";

            $count = $this->query($sql_query, ['company_id' => $company_id, "user_id" => $user_id, "job_id" => $job_id])->getRowArray();

            return !empty($count['total']) ? true : false;

        } catch (\Throwable $e) {
            log_message('critical', "[Check User ID From Job] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Insert Job                 *|
    #|******************************|
    public function insertJob($data, $db = null) {
        try {
            $db = $db ?? $this->db;
            $builder = $db->table($this->table);
            $builder->insert($data);

            $insertId = $db->insertID();

            if ($insertId !== null && $insertId !== '' && $insertId !== 0) {
                return $insertId;
            }

        } catch (\Exception $e) {
            log_message('critical', "[Insert Job] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Update Job                 *|
    #|******************************|
    public function updateJob($jobID, $data, $db = null) {
        try {
            if (empty($jobID)) throw new Exception("Empty Job ID.");

            $db = $db ?? $this->db;
            $builder = $db->table($this->table)
                          ->where('job_id', $jobID)
                          ->update($data);

            return true;
            
        } catch (\Exception $e) {
            log_message('critical', "[Update Job] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Delete Job                 *|
    #|******************************|
    public function deleteJob($jobID, $db = null) {
        try {
            if (empty($jobID)) throw new Exception("Empty Job ID.");

            $db = $db ?? $this->db;

            $builder = $db->table($this->table)
                          ->where('job_id', $jobID)
                          ->delete();

            return true;
                
        } catch (\Throwable $e) {
            log_message('critical', "[Delete Job] Error: " . $e->getMessage());
            throw $e;
        }
    }
}

