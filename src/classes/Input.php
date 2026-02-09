<?php
class Input
{

    public static function exists($type = 'post')
    {
        switch ($type) {
            case 'post':
                return !empty($_POST);
            case 'get':
                return !empty($_GET);
            default:
                return false;
        }
    }

    public static function get($item, $sanitize = false, $filter = FILTER_DEFAULT, $options = null)
    {
        if (isset($_POST[$item])) {
            $value = $_POST[$item];
        } elseif (isset($_GET[$item])) {
            $value = $_GET[$item];
        } else {
            return null; // or throw an exception
        }

        if ($sanitize) {
            return self::sanitize($value, $filter, $options);
        }

        return $value;
    }

    public static function sanitize($value, $filter = FILTER_DEFAULT, $options = null)
    {
        return filter_var($value, $filter, $options);
    }

    
    public static function validateString($input)
    {
        // Validate and sanitize the input string
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }

    public static function validateInt($input)
    {
        // Validate and sanitize the input as an integer
        return is_numeric($input);
    }

    public static function validateEmail($input)
    {
        // Validate the input as an email address
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: $input");
        }
        return $input;
    }

    public static function validateDate($input)
    {
        // Validate the input as a date (YYYY-MM-DD)
        $date = DateTime::createFromFormat('Y-m-d', $input);
        if (!$date || $date->format('Y-m-d') !== $input) {
            throw new InvalidArgumentException("Invalid date format: $input");
        }
        return $date;
    }

    public static function validateAndFormatDateForMySQL($inputDate)
    {
        // Attempt to create a DateTime object. This also validates the date.
        $date = DateTime::createFromFormat('Y-m-d', $inputDate);

        // Check if the date is valid by comparing the original string with the formatted version
        if ($date && $date->format('Y-m-d') === $inputDate) {
            // The date is valid, return it in MySQL format
            return $inputDate; // Or return $date->format('Y-m-d') if you need to ensure formatting
        } else {
            // The date is invalid
            return false;
        }
    }
    public static function validateAndFormatDateTimeForMySQL1($inputDate)
    {
        // Attempt to create a DateTime object. This also validates the date.
        $date = DateTime::createFromFormat('Y-m-d H:i', $inputDate);

        // Check if the date is valid by comparing the original string with the formatted version
        if ($date && $date->format('Y-m-d H:i') === $inputDate) {
            // The date is valid, return it in MySQL format
            return $inputDate; // Or return $date->format('Y-m-d') if you need to ensure formatting
        } else {
            // The date is invalid
            return false;
        }
    }

    public static function validateAndFormatDateTimeForMySQL($inputDate)
    {
        // Attempt to create a DateTime object from the new format
        $date = DateTime::createFromFormat('m/d/Y h:i A', $inputDate);

        // Check if the date is valid by comparing the original string with the formatted version
        if ($date && $date->format('m/d/Y h:i A') === $inputDate) {
            // The date is valid, return it in MySQL format
            return $date->format('Y-m-d H:i:s'); // Format suitable for MySQL
        } else {
            // The date is invalid
            return false;
        }
    }

    public static function validateAndFormatTimeForMySQL($inputTime)
    {
        // Attempt to create a DateTime object from 12-hour format with seconds
        $time12HourWithSeconds = DateTime::createFromFormat('h:i:s A', $inputTime);

        // Check if the 12-hour time with seconds is valid
        if ($time12HourWithSeconds && $time12HourWithSeconds->format('h:i:s A') === $inputTime) {
            // The time is valid, return it in MySQL format
            return $time12HourWithSeconds->format('H:i:s'); // Format suitable for MySQL
        }

        // Attempt to create a DateTime object from 12-hour format without seconds
        $time12Hour = DateTime::createFromFormat('h:i A', $inputTime);

        // Check if the 12-hour time without seconds is valid
        if ($time12Hour && $time12Hour->format('h:i A') === $inputTime) {
            // The time is valid, return it in MySQL format
            return $time12Hour->format('H:i:s'); // Format suitable for MySQL
        }

        // Attempt to create a DateTime object from 24-hour format with seconds
        $time24HourWithSeconds = DateTime::createFromFormat('H:i:s', $inputTime);

        // Check if the 24-hour time with seconds is valid
        if ($time24HourWithSeconds && $time24HourWithSeconds->format('H:i:s') === $inputTime) {
            // The time is valid, return it in MySQL format
            return $time24HourWithSeconds->format('H:i:s'); // Format suitable for MySQL
        }

        // Attempt to create a DateTime object from 24-hour format without seconds
        $time24Hour = DateTime::createFromFormat('H:i', $inputTime);

        // Check if the 24-hour time without seconds is valid
        if ($time24Hour && $time24Hour->format('H:i') === $inputTime) {
            // The time is valid, return it in MySQL format
            return $time24Hour->format('H:i:s'); // Format suitable for MySQL
        }

        // The time is invalid
        return false;
    }



    public static function validateTime($input)
    {
        // Validate the input as a time (HH:MM:SS)
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $input)) {
            throw new InvalidArgumentException("Invalid time format: $input");
        }
        return $input;
    }

    public static function validateOperator($operator)
    {
        // Validate that the operator is allowed
        $allowedOperators = ['=', '<', '>', '<=', '>=', 'LIKE'];
        if (!in_array($operator, $allowedOperators)) {
            throw new InvalidArgumentException("Invalid operator: $operator");
        }
        return $operator;
    }
}