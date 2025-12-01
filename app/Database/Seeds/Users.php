<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Users\UserModel;

class Users extends Seeder
{
    protected $helpers = ['misc_helper'];

    public function run()
    {
        #|*****************************|
        #|* Helpers                   *|
        #|*****************************|
        helper('misc_helper');
        
        $users = [
            [
                'name'       => normalize_string('Usuario pra teste'),
                'username'   => normalize_username('usuario'),
                'password'   => password_hash('usuario', PASSWORD_BCRYPT),
                'phone'      => format_phone_number('11998887766'),
                'email'      => normalize_email('usuario.random@gmail.com'),
                'experience' => esc(trim('Atuei por 37 segundos como desenvolvedor antes de ser demitido por dar delete sem where na base legada da empresa (não tinha backup)')),
                'education'  => esc(trim('Formado na arte da gambiarra com os xará.')),
                'account_role' => 'user'
            ],
            [
                'name'       => normalize_string('João Henrique Silva'),
                'username'   => normalize_username('joaohenrique'),
                'password'   => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'      => format_phone_number('11998887766'),
                'email'      => normalize_email('joao.henrique@example.com'),
                'experience' => esc(trim('Atuou por 3 anos como assistente administrativo, apoiando processos internos, controle de documentos e atendimento a clientes. Participou de projetos internos focados em melhoria de eficiência operacional.')),
                'education'  => esc(trim('Formado em Administração pela Universidade Federal. Participou de cursos complementares de gestão de projetos e organização empresarial.')),
                'account_role' => 'user'
            ],
            [
                'name'       => normalize_string('Mariana Costa Ferreira'),
                'username'   => normalize_username('marianacf'),
                'password'   => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'      => format_phone_number('21997766554'),
                'email'      => normalize_email('mariana.ferreira@example.com'),
                'experience' => esc(trim('Experiência de 2 anos em atendimento ao cliente, resolução de demandas e suporte comercial. Participou da implementação de processos de onboarding de novos clientes.')),
                'education'  => esc(trim('Graduada em Comunicação Social. Possui cursos adicionais em atendimento, negociação e comunicação empresarial.')),
                'account_role' => 'user'
            ],
            [
                'name'       => normalize_string('Carlos Eduardo Rocha'),
                'username'   => normalize_username('carloseduardo'),
                'password'   => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'      => format_phone_number('31987654321'),
                'email'      => normalize_email('carlos.rocha@example.com'),
                'experience' => esc(trim('Atuou como desenvolvedor júnior em projetos web utilizando PHP, JavaScript e PostgreSQL. Suporte em integrações REST e automação de processos.')),
                'education'  => esc(trim('Formado em Sistemas de Informação. Cursos adicionais em desenvolvimento web, bancos de dados e arquitetura de APIs.')),
                'account_role' => 'user'
            ],
            [
                'name'       => normalize_string('Ana Luísa Martins'),
                'username'   => normalize_username('analmartins'),
                'password'   => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'      => format_phone_number('41991234567'),
                'email'      => normalize_email('ana.martins@example.com'),
                'experience' => esc(trim('Experiência com marketing digital, criação de conteúdo e gestão de redes sociais. Atuou em campanhas de divulgação para pequenas empresas.')),
                'education'  => esc(trim('Graduada em Marketing. Formação complementar em branding, tráfego pago e copywriting.')),
                'account_role' => 'user'
            ],
            [
                'name'       => normalize_string('Rodrigo Almeida Souza'),
                'username'   => normalize_username('rodrigoasouza'),
                'password'   => password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT),
                'phone'      => format_phone_number('51999887766'),
                'email'      => normalize_email('rodrigo.souza@example.com'),
                'experience' => esc(trim('Trabalhou como técnico de suporte por 4 anos, realizando manutenção de hardware, suporte a sistemas internos e atendimento remoto a usuários.')),
                'education'  => esc(trim('Curso técnico em Informática e certificação em suporte a infraestrutura.')),
                'account_role' => 'user'
            ],
        ];

        $db = \Config\Database::connect();

        foreach ($users as $user) {
            $db->table('users')->insert($user);
        }
    }
}
