<?php

class BrutisError {
    private static $_instance = NULL;

    private function __construct() {
    /* Function __construct - instantiate private class
        void = __construct()
    */
    }

    private function __clone() {
    /* Function __clone - make clone private
        void = __clone()
    */
    }

    public static function BrutisErrorHandler
        ($errno, $errstr, $errfile, $errline){

        if (!isset(self::$_instance)) {
            self::$_instance = new BrutisError();
        }

        $error_level = error_reporting();
        if (($errno & $error_level) != $errno) {
            return TRUE;
        }
        switch ($errno) {
            case E_ERROR:
                echo "\n[FATAL_ERROR] $errstr\n";
                echo " in $errfile on line $errline\n";
                echo "Aborting...\n";
                exit(1);
            break;
            case E_WARNING:
                echo "\n[WARNING] $errstr\n";
                echo " in $errfile on line $errline\n";
            break;
            case E_PARSE:
                echo "\n[PARSE_ERROR] $errstr\n";
                echo " in $errfile on line $errline\n";
                echo "Aborting...\n";
                exit(1);
            break;
            case E_USER_ERROR:
                echo "\n[ERROR] $errstr\n";
                echo " in $errfile on line $errline\n";
                echo "Aborting...\n";
                exit(1);
            break;
            case E_USER_WARNING:
                echo "\n[WARNING] $errstr\n";
                echo " in $errfile on line $errline\n";
            break;
            case E_USER_NOTICE:
                echo "\n[USER_NOTICE] $errstr\n";
                echo " in $errfile on line $errline\n";
            break;
            case E_NOTICE:
                echo "\n[NOTICE] $errstr\n";
                echo " in $errfile on line $errline\n";
                StatService::newEvent($errstr . ". Sleeping 5 seconds");
                echo "Sleeping 5 seconds\n";
                sleep(5);
            break;
            default:
                echo "\n[DEBUG:$errno] $errstr\n";
                echo "    @ line $errline of $errfile\n";
            break;
        }
        return true;
    }
}
?>
