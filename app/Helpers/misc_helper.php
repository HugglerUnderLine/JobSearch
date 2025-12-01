<?php

if (!function_exists('normalize_spaces')) {
    function normalize_spaces($name) {
        $name = preg_replace('/\s+/', ' ', trim($name));
        return $name;
    }

}

if (!function_exists('normalize_string')) {
    function normalize_string($name) {
        if (empty($name)) return null;

        $name = trim($name);

        if (empty($name)) return null;

        $name = mb_strtoupper(remove_accents(normalize_spaces($name)));
        return esc($name);
    }
}

if (!function_exists('normalize_email')) {
    function normalize_email($email) {
        $email = trim($email);
        if (empty($email)) return null;

        $email = mb_strtolower(remove_accents(normalize_spaces($email)));
        return esc($email);
    }
}

if (!function_exists('normalize_username')) {
    function normalize_username($name) {
        $name = trim($name);
        if (empty($name)) return null;

        $name = strtolower(remove_accents(normalize_spaces($name)));
        return esc($name);
    }
}

if (!function_exists('normalize_job_area')) {
    function normalize_job_area($area) {
        if (!is_string($area)) return null;

        // Final areas
        $availableJobs = [
            'Administração',
            'Agricultura',
            'Artes',
            'Atendimento ao Cliente',
            'Comercial',
            'Comunicação',
            'Construção Civil',
            'Consultoria',
            'Contabilidade',
            'Design',
            'Educação',
            'Engenharia',
            'Finanças',
            'Jurídica',
            'Logística',
            'Marketing',
            'Produção',
            'Recursos Humanos',
            'Saúde',
            'Segurança',
            'Tecnologia da Informação',
            'Telemarketing',
            'Vendas',
            'Outros',
        ];

        // Input normalization
        $clean = trim($area);
        if ($clean === '') return null;

        // Remove accents & extra spaces
        $clean = normalize_spaces(remove_accents($clean));

        // Keeps only characters, numbers and spaces
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $clean);

        // LOWER
        $clean = strtolower($clean);

        /**
         * Internal mapping for comparision
         * key → normalized value
         */
        $normalizedMap = [];

        foreach ($availableJobs as $job) {
            $key = strtolower(remove_accents(normalize_spaces($job)));
            $key = preg_replace('/[^a-zA-Z0-9\s]/', '', $key); // Same input clean
            $normalizedMap[$key] = $job;
        }

        // Verify exact match
        if (array_key_exists($clean, $normalizedMap)) {
            return $normalizedMap[$clean];
        }

        return null;
    }
}


if (!function_exists('remove_accents')) {
    function remove_accents($str) {
        $str = preg_replace('/[áàãâä]/u', 'a', $str);
        $str = preg_replace('/[ÁÀÃÂÄ]/u', 'A', $str);
        $str = preg_replace('/[éèẽêë]/u', 'e', $str);
        $str = preg_replace('/[ÉÈẼÊË]/u', 'E', $str);
        $str = preg_replace('/[íìĩîï]/u', 'i', $str);
        $str = preg_replace('/[ÍÌĨÎÏ]/u', 'I', $str);
        $str = preg_replace('/[óòõôö]/u', 'o', $str);
        $str = preg_replace('/[ÓÒÕÔÖ]/u', 'O', $str);
        $str = preg_replace('/[úùũûü]/u', 'u', $str);
        $str = preg_replace('/[ÚÙŨÛÜ]/u', 'U', $str);
        $str = preg_replace('/[ç]/u', 'c', $str);
        $str = preg_replace('/[Ç]/u', 'c', $str);
        return $str;
    }
}

if (!function_exists('format_phone_number')) {
    function format_phone_number($phone) {
        $phone = trim($phone);
        if (empty($phone)) return null;

        # Verify if is already in final format
        if (preg_match('/^\(\d{2}\)\d{4,5}-\d{4}$/', $phone)) {
            return esc($phone); // Returns original value
        }

        # Remove anything that is not a number
        $digits = preg_replace('/\D/', '', $phone);

        # Valid phones should have between 10 and 11 digits (with area code)
        # Ex: 42 3030 3030 or 42 99191 9191
        $len = strlen($digits);
        if ($len < 10 || $len > 11) {
            return $phone; // Return the original if invalid length
        }

        # Extract parts
        $ddd = substr($digits, 0, 2);
        $main = substr($digits, 2);

        # Phones with 9 digits (cell phones)
        if ($len === 11) {
            $prefix = substr($main, 0, 5);
            $suffix = substr($main, 5);
            $formatted = sprintf('(%s)%s-%s', $ddd, $prefix, $suffix);
        } 
        # Phones with 8 digits (landlines)
        else {
            $prefix = substr($main, 0, 4);
            $suffix = substr($main, 4);
            $formatted = sprintf('(%s)%s-%s', $ddd, $prefix, $suffix);
        }

        return esc($formatted);
    }
}

if (!function_exists('valid_state')) {
    /**
     * Validate and normalize a Brazilian state name or abbreviation.
     *
     * @param string $state Input string from user
     * @return string|false Returns only the UF (e.g., "SP") or false if invalid
     */
    function valid_state($state) {
        $state = trim(strval($state));
        if (empty($state)) return null;

        # Mapping of all Brazilian states
        $states = [
            'AC'=>'Acre',
            'AL'=>'Alagoas',
            'AP'=>'Amapá',
            'AM'=>'Amazonas',
            'BA'=>'Bahia',
            'CE'=>'Ceará',
            'DF'=>'Distrito Federal',
            'ES'=>'Espírito Santo',
            'GO'=>'Goiás',
            'MA'=>'Maranhão',
            'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul',
            'MG'=>'Minas Gerais',
            'PA'=>'Pará',
            'PB'=>'Paraíba',
            'PR'=>'Paraná',
            'PE'=>'Pernambuco',
            'PI'=>'Piauí',
            'RJ'=>'Rio de Janeiro',
            'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul',
            'RO'=>'Rondônia',
            'RR'=>'Roraima',
            'SC'=>'Santa Catarina',
            'SP'=>'São Paulo',
            'SE'=>'Sergipe',
            'TO'=>'Tocantins'
        ];

        # Clean input
        $state = trim(explode('-', $state)[0]); // remove informações extras
        $normalized = mb_strtoupper(remove_accents($state));
        $normalized = preg_replace('/[^A-Z]/', '', $normalized);

        # 1. If matches a valid UF directly
        if (isset($states[$normalized])) {
            return esc($normalized);  // Return only the UF
        }

        # Normalize full state names for comparison
        $normalizedStates = [];
        foreach ($states as $uf => $name) {
            $normName = mb_strtoupper(remove_accents($name));
            $normName = preg_replace('/[^A-Z]/', '', $normName);
            $normalizedStates[$uf] = $normName;
        }

        # 2. If matches full state name
        foreach ($normalizedStates as $uf => $normName) {
            if ($normalized === $normName) {
                return esc($uf); // Return only the UF
            }
        }

        return null; // Invalid
    }
}
