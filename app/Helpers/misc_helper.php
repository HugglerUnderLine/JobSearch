<?php

if (!function_exists('normalize_spaces')) {
    function normalize_spaces($name) {
        $name = preg_replace('/\s+/', ' ', trim($name));
        return $name;
    }

}

if (!function_exists('normalize_string')) {
    function normalize_string($name) {
        $name = mb_strtoupper(remove_accents(normalize_spaces($name)));
        return $name;
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

        if (empty($phone)) {
            return $phone;
        }

        # Verify if is already in final format
        if (preg_match('/^\(\d{2}\)\d{4,5}-\d{4}$/', $phone)) {
            return $phone; // Returns original value
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

        return $formatted;
    }
}

if(!function_exists('normalize_date')) {
    function normalize_date($data)
    {
        return !empty($data) ? date_format(date_create($data), "d/m/Y") : '';
    }
}

if (!function_exists('valid_state')) {
    /**
     * Validate and normalize a Brazilian state name or abbreviation.
     *
     * @param string $state Input string from user
     * @return string|false Returns formatted state (e.g., "São Paulo (SP)") or false if invalid
     */
    function valid_state(string $state)
    {
        if (empty($state)) {
            return false;
        }

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

        # If already in final format "Name (UF)"
        if (preg_match('/^([A-ZÀ-ÿ\s]+)\s\(([A-Z]{2})\)$/u', $state, $matches)) {
            $uf = $matches[2];

            if (isset($states[$uf])) {
                return $state; // Returns the original value
            }
        }

        # Remove extra spaces, accents, special characters, and normalize case
        # Clean everything that maybe comes after "-"
        $state = explode('-', $state)[0]; 
        $state = trim($state);

        # Default normalization
        $normalized = mb_strtoupper(remove_accents($state));
        $normalized = preg_replace('/[^A-Z0-9]/', '', $normalized);

        # Build an array with normalized forms for comparison
        $normalizedStates = [];
        foreach ($states as $uf => $name) {
            $normName = mb_strtoupper(remove_accents($name));
            $normName = preg_replace('/[^A-Z0-9]/', '', $normName);
            $normalizedStates[$uf] = $normName;
        }

        # Check if input matches a valid UF directly
        if (isset($states[$normalized])) {
            return "{$states[$normalized]} ({$normalized})";
        }

        # Check if matches any normalized name
        foreach ($normalizedStates as $uf => $normName) {
            if ($normName === $normalized) {
                return "{$states[$uf]} ({$uf})";
            }
        }

        return false; // invalid state
    }
}



