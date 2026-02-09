<?php
class Session
{
    public static function exists($sessName)
    {
        return (isset($_SESSION[$sessName])) ? true : false;
    }

    public static function put($sessName, $sessValue)
    {
        if (!self::exists($sessName)) {
            return $_SESSION[$sessName] = $sessValue;
        } else {
            return $_SESSION[$sessName];
        }
        //return $_SESSION[$sessName] = $sessValue;
    }

    public static function delete($sessName)
    {
        if (self::exists($sessName)) {
            unset($_SESSION[$sessName]);
            return true;
        }
        
        return false;
    }

    public static function getSessVal($sessName)
    {
        if (self::exists($sessName)) {
            return $_SESSION[$sessName];
        }
    }

    public static function flash($sessName, $string = "")
    {
        if (self::exists($sessName)) {
            $session = self::getSessVal($sessName);
            self::delete($sessName);
            return $session;
        } else {
            self::put($sessName, $string);
        }
    }
}
