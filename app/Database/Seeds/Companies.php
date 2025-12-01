<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
use App\Models\Companies\CompanyModel;
use App\Models\Users\UserModel;

class Companies extends Seeder
{
    public function run()
    {

        #|*****************************|
        #|* Helpers                   *|
        #|*****************************|
        helper('misc_helper');
        /*
            Format:
            $userData = [
                'name'         => $newData['name'],
                'username'     => $newData['username'],
                'password'     => password_hash($newData['password'], PASSWORD_BCRYPT),
                'phone'        => $newData['phone'],
                'email'        => $newData['email'],
                'experience'   => null,
                'education'    => null,
                'account_role' => 'company',
            ];
        */
        $users = [
            [
                'name'          => normalize_string('Tech Vision Solutions'),
                'username'      => normalize_username('techvision'),
                'password'      => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'         => format_phone_number('41998998877'),
                'email'         => normalize_email('contact@techvision.com'),
                'account_role'  => 'company',
                'education'     => null,
                'experience'    => null
            ],
            [
                'name'          => normalize_string('MetalCorp Indústrias'),
                'username'      => normalize_username('metalcorp'),
                'password'      => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'         => format_phone_number('31988776655'),
                'email'         => normalize_email('support@metalcorp.com'),
                'account_role'  => 'company',
                'education'     => null,
                'experience'    => null
            ],
            [
                'name'          => normalize_string('GreenData Analytics'),
                'username'      => normalize_username('greendata'),
                'password'      => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'         => format_phone_number('11991234567'),
                'email'         => normalize_email('info@greendata.com'),
                'account_role'  => 'company',
                'education'     => null,
                'experience'    => null
            ],
            [
                'name'          => normalize_string('Alphasoft Digital'),
                'username'      => normalize_username('alphasoft'),
                'password'      => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'         => format_phone_number('21998887766'),
                'email'         => normalize_email('contact@alphasoft.com'),
                'account_role'  => 'company',
                'education'     => null,
                'experience'    => null
            ],
            [
                'name'          => normalize_string('UrbanBuild Construtora'),
                'username'      => normalize_username('urbanbuild'),
                'password'      => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'         => format_phone_number('51988774466'),
                'email'         => normalize_email('admin@urbanbuild.com'),
                'account_role'  => 'company',
                'education'     => null,
                'experience'    => null
            ],
        ];


        /*
            Format:
            $companyData = [
                'business'     => $newData['business'],
                'street'       => $newData['street'],
                'number'       => $newData['number'],
                'city'         => $newData['city'],
                'state'        => $newData['state'],
            ];
        */
        $companies = [
            [
                'business' => normalize_string('Tecnologia da Informação'),
                'street'   => normalize_string('Rua das Inovações'),
                'number'   => '1200',
                'city'     => normalize_string('Curitiba'),
                'state'    => valid_state('PR')
            ],
            [
                'business' => normalize_string('Metalurgia'),
                'street'   => normalize_string('Avenida Industrial'),
                'number'   => '455',
                'city'     => normalize_string('Belo Horizonte'),
                'state'    => valid_state('MG')
            ],
            [
                'business' => normalize_string('Análise de Dados'),
                'street'   => normalize_string('Rua Verde Horizonte'),
                'number'   => '89',
                'city'     => normalize_string('São Paulo'),
                'state'    => valid_state('SP')
            ],
            [
                'business' => normalize_string('Desenvolvimento de Software'),
                'street'   => normalize_string('Avenida Central'),
                'number'   => '200',
                'city'     => normalize_string('Rio de Janeiro'),
                'state'    => valid_state('RJ')
            ],
            [
                'business' => normalize_string('Construção Civil'),
                'street'   => normalize_string('Rua da Estrutura'),
                'number'   => '300',
                'city'     => normalize_string('Porto Alegre'),
                'state'    => valid_state('RS')
            ],
        ];

        $db = \Config\Database::connect();

        foreach ($users as $index => $u) {

            // Insert into users
            $db->table('users')->insert($u);
            $userId = $db->insertID();

            // Link company with user_id
            $company = $companies[$index];
            $company['user_id'] = $userId;

            $db->table('companies')->insert($company);
        }
    }
}
