<?php

date_default_timezone_set('Asia/Karachi');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
/*==================================================
    GLOBAL TECH UNIVERSITY (GTU)
    Configuration File
==================================================*/

// Prevent multiple loading
if (!defined('GTU_CONFIG')) {

    define('GTU_CONFIG', true);

    // Website Settings
    define('SITE_NAME', 'Global Tech University');
    define('SITE_SHORT_NAME', 'GTU');
    define('SITE_URL', 'http://localhost/GTU_ERP');

    // Timezone
    define('TIMEZONE', 'Asia/Karachi');
    date_default_timezone_set(TIMEZONE);

    // Start Session Only Once
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

}