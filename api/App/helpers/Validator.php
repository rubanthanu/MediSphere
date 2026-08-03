<?php

class Validator
{
    
    public static function required(array $data, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                return false;
            }
        }
        return true;
    }

  
    public static function email(string $email): bool
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

  
    public static function validStatus(string $status): bool
    {
        $allowed = ['pending', 'confirmed', 'cancelled', 'completed'];
        return in_array($status, $allowed, true);
    }

   
    public static function validGender(string $gender): bool
    {
        $allowed = ['male', 'female', 'other'];
        return in_array(strtolower(trim($gender)), $allowed, true);
    }

  
    public static function validDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', trim($date));
        return $d && $d->format('Y-m-d') === trim($date);
    }


    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
