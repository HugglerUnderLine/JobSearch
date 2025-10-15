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

    # Generic Method for usage of Query Builder UPDATE and INSERT
    public function companyUpsert($data, $id = null) {
        try {
            $this->db->transBegin();
            $builder = $this->db->table($this->table);

            if (!empty($id)) {
                # Update
                $builder->where('company_id', $id);
                $result = $builder->update($data);
            } else {
                # Insert
                $result = $builder->insert($data);
            }

            # Verify transaction status and query result
            if ($this->db->transStatus() === false || !$result) {
                $error = $this->db->error();
                log_message('error', '[DB ERROR - COMPANY UPSERT] Code: ' . $error['code'] . ' | Message: ' . $error['message']);
                $this->db->transRollback();
                return false;
            }

            $this->db->transCommit();
            return true;

        } catch (\Exception $e) {
            log_message('critical', '[DB ERROR - COMPANY UPSERT EXCEPTION] ' . $e->getMessage());
            $this->db->transRollback();
            return false;
        }
    }

    public function insertCompany($data)
    {
        return $this->companyUpsert($data);
    }

    public function updateCompany($id, $data)
    {
        return $this->companyUpsert($data, $id);
    }

}
