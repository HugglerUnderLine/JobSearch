<?php

namespace App\Models\System;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserModel extends Model
{
    protected $table            = 'platform_user';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_uuid', 'user_name', 'user_email', 'user_password', 'user_role', 'user_is_active', 'user_terms_accepted', 'kentik_customer_id', 'corero_customer_id'];

    protected bool $allowEmptyInserts = false;

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

    # Generate a random UUID
    public function generateUUID() {
        $uuid = Uuid::uuid4()->toString(); 

        # Check if the UUID already exists
        $sql = "SELECT COUNT(user_uuid) AS total FROM platform_user WHERE user_uuid = :uuid:";
        $validUUID = $this->query($sql, ['uuid' => $uuid])->getResultArray()[0]['total'];

        # If the UUID already exists, generate a new one
        while(!empty($validUUID)) {
            $uuid = Uuid::uuid4()->toString();
            $validUUID = $this->query($sql, ['uuid' => $uuid])->getResultArray()[0]['total'];
        }

        return $uuid;
    }

    
    public function passwordGenerator() {
        /* 
            This method has been created to generate a first access random password.
            The password will be used for the first user login, and should be changed on first access.
        */

        $upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowerChars = strtolower($upperChars);
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*()-_=+[]{}|;:,.<>?';

        # Join all characters into a single string
        $allChars = $upperChars . $lowerChars . $numbers . $specialChars;

        # Ensure that the password have at least one of each character type
        $password = [
            $upperChars[random_int(0, strlen($upperChars) - 1)],
            $lowerChars[random_int(0, strlen($lowerChars) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $specialChars[random_int(0, strlen($specialChars) - 1)],
        ];

        # Fill the rest of the password with random characters
        # 12 is the maximum length of the password
        for ($i = 4; $i < 12; $i++) {
            $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        # Shuffle the password characters
        shuffle($password);

        return implode('', $password);

    }


    public function authUser($email) {

        $sql_query = 'SELECT platform_user.user_id,
                             platform_user.user_uuid,
                             platform_user.user_email,
                             platform_user.user_password,
                             platform_user.user_name,
                             platform_user.user_role,
                             platform_user.user_terms_accepted::boolean AS user_terms_accepted,
                             platform_user.kentik_customer_id,
                             platform_user.corero_customer_id
                      FROM platform_user
                      WHERE platform_user.user_email = :email:
                      AND platform_user.user_is_active = TRUE';

        return $this->query($sql_query, ['email' => $email])->getRowArray();

    }

    # Generic Method for usage of Query Builder UPDATE and INSERT
    public function userUpsert($data, $id = null) {
        try {
            $builder = $this->db->table($this->table);

            if (!empty($id)) {
                # If isset and not empty primaryKey, update
                $builder->where('user_id', $id);
                $builder->update($data);
                
                return $this->db->transStatus();
            }

            # else, performs insert
            $builder->insert($data);
            return $this->db->transStatus();
        } catch (\Exception $e) {
            // log_message('critical', 'Upsert error: ' . $e->getMessage());
            return false;
        }
    }


    public function insertUser($data) {
        return $this->userUpsert($data);
    }


    public function updateUser($id, $data) {
        return $this->userUpsert($data, $id);
    }


    public function updatePassword($id, $password) {
        $builder = $this->db->table($this->table);

        $builder->where('user_id', $id);
        $builder->update(['user_password' => $password]);

        return $this->db->transStatus();
    }


    # Transactions to ensure that the ACID properties are followed
    public function startTransaction() {
        $this->db->transBegin();
    }
    

    public function commitTransaction() {
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
        } else {
            $this->db->transCommit();
        }
    }
    

    public function rollbackTransaction() {
        $this->db->transRollback();
    }

}
