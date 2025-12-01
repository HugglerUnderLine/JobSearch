<?php

namespace App\Models\Users;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'username', 'password', 'email', 'phone', 'account_role'];

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
    #|* Auth User                  *|
    #|******************************|
    public function authUser($username) {
        try {
            $sql_query = "
                SELECT users.user_id,
                       users.name,
                       users.username,
                       users.password,
                       users.email,
                       users.phone,
                       users.experience,
                       users.education,
                       users.account_role
                FROM users
                WHERE users.username = :username:";

            return $this->query($sql_query, ['username' => $username])->getRowArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Auth User] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Get User Data By ID        *|
    #|******************************|
    public function getUserDataByID($user_id) {
        try {
            $sql_query = "
                SELECT users.user_id,
                       users.username,
                       users.name,
                       users.password,
                       users.email,
                       users.phone,
                       users.experience,
                       users.education,
                       users.account_role
                FROM users
                WHERE users.user_id = :user_id:";

            return $this->query($sql_query, ['user_id' => $user_id])->getRowArray();

        } catch (\Throwable $e) {
            log_message('critical', "[Get User Data By ID] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|************************************|
    #|* Is Unique Name (User or Company) *|
    #|************************************|
    public function isUniqueName($name, $role) {
        try {
            $sql_query = "
                SELECT COUNT(*) AS total
                FROM users
                WHERE users.name = :name:
                AND users.account_role = :role:";

            $count = $this->query($sql_query, ['name' => $name, 'role' => $role])->getRowArray();

            return empty($count['total']) ? true : false; 

        } catch (\Throwable $e) {
            log_message('critical', "[Is Unique Name] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Insert User                *|
    #|******************************|
    public function insertUser($data, $db = null) {
        try {
            $db = $db ?? $this->db;
            $builder = $db->table($this->table);
            $builder->insert($data);

            $insertId = $db->insertID();

            if ($insertId !== null && $insertId !== '' && $insertId !== 0) {
                return $insertId;
            }

        } catch (\Exception $e) {
            log_message('critical', "[Insert User] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Update User                *|
    #|******************************|
    public function updateUser($userID, $data, $db = null) {
        try {
            if (empty($userID)) throw new Exception("Empty User ID.");

            $db = $db ?? $this->db;
            $builder = $db->table($this->table)
                          ->where('user_id', $userID)
                          ->update($data);

            return true;
            
        } catch (\Exception $e) {
            log_message('critical', "[Update User] Error: " . $e->getMessage());
            throw $e;
        }
    }


    #|******************************|
    #|* Delete User                *|
    #|******************************|
    public function deleteUser($userID, $db = null) {
        try {
            if (empty($userID)) throw new Exception("Empty User ID.");

            $db = $db ?? $this->db;

            $builder = $db->table($this->table)
                          ->where('user_id', $userID)
                          ->delete();

            return true;
                
        } catch (\Throwable $e) {
            log_message('critical', "[Delete User] Error: " . $e->getMessage());
            throw $e;
        }
    }
}
