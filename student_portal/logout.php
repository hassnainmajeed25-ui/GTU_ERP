<?php

session_start();

require_once '../config/config.php';

/*=========================================
DESTROY ALL SESSION
=========================================*/

$_SESSION = array();

/*=========================================
DESTROY SESSION
=========================================*/

session_destroy();

/*=========================================
REMOVE SESSION COOKIE
=========================================*/

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(

        session_name(),

        '',

        time() - 42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );

}

/*=========================================
REDIRECT TO LOGIN PAGE
=========================================*/

header("Location: login.php");

exit;

?>