<?php

namespace App\Models\System;

use CodeIgniter\Model;

class JobApplicationModel extends Model
{
    protected $table            = 'job_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['job_id', 'user_id', 'user_name', 'user_email', 'user_phone', 'user_education', 'user_experience', 'feedback'];

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


    #|**************************************************|
    #|* Verify if an user has already applied to a job *|
    #|**************************************************|
    public function verifyApplication($job_id, $user_id) {
        try {
            $sql_select = "
                SELECT COUNT(*) as total
                FROM job_applications
                WHERE job_applications.job_id = :job_id:
                AND job_applications.user_id = :user_id:";

            $count = $this->query($sql_select, ['job_id' => $job_id, 'user_id' => $user_id])->getRowArray();

            return !empty($count['total']) ? true : false;

        } catch (\Throwable $e) {
            log_message('critical', "[Verify Application] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|*****************************************************************|
    #|* Verify if an company has already provided a feedback for user *|
    #|*****************************************************************|
    public function verifyFeedback($job_id, $user_id) {
        try {
            $sql_select = "
                SELECT COUNT(*) as total
                FROM job_applications
                WHERE job_applications.job_id = :job_id:
                AND job_applications.user_id = :user_id:
                AND job_applications.feedback IS NOT NULL";

            $count = $this->query($sql_select, ['job_id' => $job_id, 'user_id' => $user_id])->getRowArray();

            return !empty($count['total']) ? true : false;

        } catch (\Throwable $e) {
            log_message('critical', "[Verify Feedback] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|**********************************|
    #|* Get Applied Jobs By User ID    *|
    #|**********************************|
    public function getAppliedJobsByUserID($user_id) {
        try {
            $sql_query = "
                SELECT job_applications.job_id,
                       jobs.title,
                       jobs.area,
                       users.name as company,
                       jobs.description,
                       jobs.state,
                       jobs.city,
                       jobs.salary::NUMERIC as salary,
                       users.email as contact,
                       CASE
                            WHEN job_applications.feedback IS NULL THEN NULL
                            ELSE job_applications.feedback
                       END AS feedback
                FROM job_applications
                INNER JOIN jobs ON job_applications.job_id = jobs.job_id
                INNER JOIN companies ON jobs.company_id = companies.company_id
                INNER JOIN users ON companies.user_id = users.user_id
                WHERE job_applications.user_id = :user_id:";

            return $this->query($sql_query, ['user_id' => $user_id,])->getResultArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Get Applied Jobs By User ID] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|**************************************|
    #|* Get Candidates By Company & Job ID *|
    #|**************************************|
    public function getCandidatesByJobID($company_id, $job_id) {
        try {
            $sql_query = "
                SELECT job_applications.user_id,
                       job_applications.user_name AS name,
                       job_applications.user_email AS email,
                       job_applications.user_phone AS phone,
                       job_applications.user_education AS education,
                       job_applications.user_experience AS experience
                FROM job_applications
                INNER JOIN jobs ON job_applications.job_id = jobs.job_id
                WHERE job_applications.job_id = :job_id:
                AND jobs.company_id = :company_id:";

            return $this->query($sql_query, ['job_id' => $job_id, 'company_id' => $company_id])->getResultArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Get Candidates By Job ID] Error: " . $e->getMessage());
            throw $e;
        }
    }



    #|******************************|
    #|* Insert Application         *|
    #|******************************|
    public function insertApplication($data, $db = null) {
        try {
            $db = $db ?? $this->db;
            $builder = $db->table($this->table);
            $builder->insert($data);

            $insertId = $db->insertID();

            if ($insertId !== null && $insertId !== '' && $insertId !== 0) {
                return $insertId;
            }

        } catch (\Exception $e) {
            log_message('critical', "[Insert Application] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Update Feedback            *|
    #|******************************|
    public function updateFeedback($jobID, $userID, $message, $db = null) {
        try {
            if (empty($jobID)) throw new Exception("Empty Job ID.");

            $db = $db ?? $this->db;
            $builder = $db->table($this->table)
                          ->where('job_id', $jobID)
                          ->where('user_id', $userID)
                          ->update(['feedback' => $message]);

            return true;
            
        } catch (\Exception $e) {
            log_message('critical', "[Update Feedback] Error: " . $e->getMessage());
            throw $e;
        }
    }
}
