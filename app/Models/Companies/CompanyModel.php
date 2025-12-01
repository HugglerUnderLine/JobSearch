<?php

namespace App\Models\Companies;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table            = 'companies';
    protected $primaryKey       = 'company_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'company_name', 'business', 'street', 'number', 'city', 'state'];

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
    #|* Get Company Data By ID     *|
    #|******************************|
    public function getCompanyDataByID($user_id) {
        try {
            $sql_query = "
                SELECT companies.company_id,
                       companies.user_id,
                       users.username,
                       users.name,
                       users.email,
                       users.phone,
                       users.account_role,
                       companies.business,
                       companies.street,
                       companies.number,
                       companies.city,
                       companies.state
                FROM companies
                INNER JOIN users ON companies.user_id = users.user_id
                WHERE companies.user_id = :user_id:
                AND users.account_role = 'company'";

            return $this->query($sql_query, ['user_id' => $user_id])->getRowArray();
        } catch (\Throwable $e) {
            log_message('critical', '[Get Company Data By ID] Error: ' . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Insert Company             *|
    #|******************************|
    public function insertCompany($data, $db = null) {
        try {
            $db = $db ?? $this->db;
            $builder = $db->table($this->table);
            $builder->insert($data);

            $insertId = $db->insertID();

            if ($insertId !== null && $insertId !== '' && $insertId !== 0) {
                return $insertId;
            }

        } catch (\Exception $e) {
            log_message('critical', "[Insert Company] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Update Company             *|
    #|******************************|
    public function updateCompany($userID, $data, $db = null) {
        try {
            if (empty($userID)) throw new Exception("Empty User ID.");

            $db = $db ?? $this->db;

            $builder = $db->table($this->table)
                          ->where('user_id', $userID)
                          ->update($data);

            return true;
            
        } catch (\Exception $e) {
            log_message('critical', "[Update Company] Error: " . $e->getMessage());
            throw $e;
        }
    }
}
