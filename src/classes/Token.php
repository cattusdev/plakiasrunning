<?php
    class Token
    {
        public static function genToken($tokenSess)
        {
            return Session::put(Config::get('session/'. $tokenSess), Hash::unique());
        }

        public static function checkToken($token,$sessRoot)
        {
            $sessionName = Config::get('session/' . $sessRoot);
            if(Session::exists($sessionName) && $token === Session::getSessVal($sessionName))
            {
                //Session::delete($sessionName);
                return true;
            }
            return false;
        }
    }
    