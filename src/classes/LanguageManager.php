<?php
class LanguageManager
{
    public static $GLOBAL_LANGUAGE1;

    public static function initializeLanguage()
    {
        global $GLOBAL_LANGUAGE;
        self::$GLOBAL_LANGUAGE1 = $GLOBAL_LANGUAGE;
        // Place your logic here...
        // Sample logic:
        if (isset($_GET['lang'])) {
            $GLOBAL_LANGUAGE = $_GET['lang'];
            $_SESSION['slang'] = $GLOBAL_LANGUAGE;
            setcookie('clang', $GLOBAL_LANGUAGE, time() + (3600 * 24 * 30), "/");
        } else if (isset($_SESSION['slang'])) {
            $GLOBAL_LANGUAGE = $_SESSION['slang'];
        } else if (isset($_COOKIE['clang'])) {
            $GLOBAL_LANGUAGE = $_COOKIE['clang'];
        } else {
            $GLOBAL_LANGUAGE = 'el';
        }

        self::$GLOBAL_LANGUAGE1 = $GLOBAL_LANGUAGE;
    }

    public static function returnLang()
    {
        return self::$GLOBAL_LANGUAGE1;
    }
}
