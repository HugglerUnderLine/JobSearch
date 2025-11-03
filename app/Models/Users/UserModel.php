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

    public function authUser($username) {
        $sql_query = 'SELECT users.user_id,
                             users.name,
                             users.username,
                             users.password,
                             users.email,
                             users.phone,
                             users.experience,
                             users.education,
                             users.account_role
                      FROM users
                      WHERE users.username = :username:';

        return $this->query($sql_query, ['username' => $username])->getRowArray();
    }

    public function getUserDataByID($user_id) {
        $sql_query = 'SELECT users.user_id,
                             users.username,
                             users.name,
                             users.password,
                             users.email,
                             users.phone,
                             users.experience,
                             users.education,
                             users.account_role
                      FROM users
                      WHERE users.user_id = :user_id:';

        return $this->query($sql_query, ['user_id' => $user_id])->getRowArray();
    }

    public function findUserByName($userName) {
        $sql_query = 'SELECT users.user_id,
                             users.username,
                             users.name,
                             users.password,
                             users.email,
                             users.phone,
                             users.experience,
                             users.education,
                             users.account_role
                      FROM users
                      WHERE UPPER(users.name) = :user_name:';

        return $this->query($sql_query, ['user_name' => $userName])->getRowArray();
    }

    # Generic Method for usage of Query Builder UPDATE and INSERT
    public function userUpsert($data, $id = null) {
        try {
            $this->db->transBegin();
            $builder = $this->db->table($this->table);

            if (!empty($id)) {
                # Update
                $builder->where('user_id', $id);
                $result = $builder->update($data);

                # Check for transaction errors
                if ($this->db->transStatus() === false || !$result) {
                    $error = $this->db->error();
                    log_message('error', '[DB ERROR - USER UPSERT] Code: ' . $error['code'] . ' | Message: ' . $error['message']);
                    $this->db->transRollback();
                    return false;
                }

                $this->db->transCommit();
                return $id; // Return updated user ID

            } else {
                # Insert
                $result = $builder->insert($data);

                # Check for transaction errors
                if ($this->db->transStatus() === false || !$result) {
                    $error = $this->db->error();
                    log_message('error', '[DB ERROR - USER UPSERT] Code: ' . $error['code'] . ' | Message: ' . $error['message']);
                    $this->db->transRollback();
                    return false;
                }

                $insertedId = $this->db->insertID(); // Get the newly inserted user ID
                $this->db->transCommit();
                return $insertedId; // Return the ID of the inserted user
            }

        } catch (\Exception $e) {
            log_message('critical', '[DB ERROR - USER UPSERT EXCEPTION] ' . $e->getMessage());
            $this->db->transRollback();
            return false;
        }
    }

    public function insertUser($data) {
        return $this->userUpsert($data);
    }

    public function updateUser($id, $data) {
        return $this->userUpsert($data, $id);
    }

    public function deleteUser($id) {
        try {
            log_message('info', "[DELETE USER] Attempting to delete user ID: {$id}");

            $deleted = $this->where('user_id', $id)->delete();

            if (!$deleted) {
                log_message('error', "[DELETE USER] Failed to delete user ID: {$id}. Delete returned false or 0 rows affected.");
                return false;
            }

            log_message('info', "[DELETE USER] User ID {$id} successfully deleted.");
            return true;

        } catch (\Throwable $e) {
            log_message('error', "[DELETE USER] Exception while deleting user ID {$id}: " . $e->getMessage());
            return false;
        }
    }


}
