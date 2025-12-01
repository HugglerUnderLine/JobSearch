<?php

namespace App\Models;

use CodeIgniter\Model;

class LoggedUsersModel extends Model
{
    protected $table            = 'logged_users';
    protected $primaryKey       = 'logged_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['jwt_token', 'user_id', 'username', 'name', 'email', 'account_role', 'ip'];

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
    #|* Get User Data By ID        *|
    #|******************************|
    public function getLoggedUsers() {
        try {
            $sql_query = "
                SELECT logged_users.jwt_token,
                       logged_users.user_id, 
                       logged_users.username, 
                       logged_users.name, 
                       logged_users.email, 
                       logged_users.account_role, 
                       logged_users.ip
                FROM logged_users";

            return $this->query($sql_query)->getResultArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Get User Data By ID] Error: " . $e->getMessage());
            throw $e;
        }
    }
}
