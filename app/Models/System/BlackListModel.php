<?php

namespace App\Models\System;

use CodeIgniter\Model;

class BlackListModel extends Model
{
    protected $table            = 'blacklist';
    protected $primaryKey       = 'row_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['token', 'inserted_at'];

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

    public function verify_token($token) {
        try {
            $sql_query = "
                SELECT blacklist.row_id,
                       blacklist.token,
                       blacklist.inserted_at
                FROM blacklist
                WHERE blacklist.token = :token:";

            $result = $this->query($sql_query, ['token' => $token])->getRowArray();
            return empty($result) ? true : false;

        } catch (\Throwable $e) {
            log_message('critical', '[Verify Token] Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function add_token($token, $db = null) {
        try {
            if (empty($token)) throw new Exception("Empty Token.");

            $db = $db ?? $this->db;
            $builder = $db->table($this->table);

            $data = [
                'token' => $token
            ];

            $builder->insert($data);

            $insertId = $db->insertID();

            if ($insertId !== null && $insertId !== '' && $insertId !== 0) {
                return $insertId;
            }

        } catch (\Throwable $e) {
            log_message('critical', '[Add Token to Blacklist] Error: ' . $e->getMessage());
            throw $e;
        }
    }

}
