<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;
use App\Models\Companies\CompanyModel;
use App\Models\System\JobModel;

class Jobs extends Seeder
{
    public function run()
    {
        #|*****************************|
        #|* Helpers                   *|
        #|*****************************|
        helper('misc_helper');

        $faker = Factory::create('pt_BR');

        $companyModel = new CompanyModel();
        $jobModel     = new JobModel();

        // Lista oficial de áreas permitidas
        $areas = [
            'Administração','Agricultura','Artes','Atendimento ao Cliente','Comercial','Comunicação',
            'Construção Civil','Consultoria','Contabilidade','Design','Educação','Engenharia','Finanças',
            'Jurídica','Logística','Marketing','Produção','Recursos Humanos','Saúde','Segurança',
            'Tecnologia da Informação','Telemarketing','Vendas','Outros'
        ];

        // Estados válidos (para função valid_state)
        $states = [
            'AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT',
            'PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO'
        ];

        $companies = $companyModel->findAll();

        if (empty($companies)) {
            echo "Nenhuma empresa encontrada. Execute o seeder de empresas antes.\n";
            return;
        }

        foreach ($companies as $company) {

            for ($i = 0; $i < 2; $i++) {

                $title = $faker->jobTitle;
                $area  = $faker->randomElement($areas);
                $city  = $faker->city;
                $state = $faker->randomElement($states);

                $newData = [
                    'company_id'  => $company['company_id'],
                    'title'       => normalize_string($title),
                    'area'        => normalize_job_area($area),
                    'description' => esc(trim($faker->paragraph(4))),
                    'state'       => valid_state($state),
                    'city'        => normalize_string($city),
                    'salary'      => $faker->randomFloat(2, 1500, 30000),
                ];

                $jobModel->insert($newData);
            }
        }
    }
}
