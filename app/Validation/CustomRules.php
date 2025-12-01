<?php

namespace App\Validation;

class CustomRules
{
    # Ensure that the password doesen't contains white spaces
    public function no_spaces(string $str, string &$error = null): bool
    {
        if (strpos($str, ' ') !== false) {
            // $error = 'The field cannot contain spaces.';
            return false;
        }
        return true;
    }

    public function no_special_chars(string $str, string &$error = null): bool
    {
        # Verify if there is any character outside A-Z, a-z, 0-9, space
        if (preg_match('/[^a-zA-Z0-9 ]/', $str)) {
            # Apply error message
            // $error = 'The field cannot contain special characters.';
            return false;
        }
        return true;
    }

    public function valid_state_rule(string $str, string &$error = null): bool
    {
        // Use the helper to validate the state
        $result = valid_state($str);

        if ($result === false) {
            $error = 'Please select a valid state.';
            return false;
        }

        return true;
    }

    public function is_boolean($value): bool
    {
        # Validate only if the value is a boolean
        return in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false', 't', 'f', 'yes', 'no'], true);
    }

    public function valid_ip_with_cidr(string $str, ?string $fields = null, array $data = []): bool
    {
        # Regex for validating IPv4 address with optional CIDR
        $ipv4Regex = '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\/([0-9]|[1-2][0-9]|3[0-2]))?$/';

        # Simplified regex for validating IPv6 address with optional CIDR
        $ipv6Regex = '/^([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|(([0-9a-fA-F]{1,4}:){1,7}:)|(([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4})|(([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2})|(([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3})|(([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4})|(([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5})|([0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6}))|(:((:[0-9a-fA-F]{1,4}){1,7}|:))|::(ffff(:0{1,4}){0,1}:){0,1}(([0-9a-fA-F]{1,4}:){1,4}:|:)((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3,3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\/([0-9]|[1-9][0-9]|1[0-1][0-9]|12[0-8]))?$/i';

        return (bool) preg_match($ipv4Regex, $str) || (bool) preg_match($ipv6Regex, $str);
    }
}
