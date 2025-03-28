<?php
if (!defined('SECRET_KEY')) {
    define('SECRET_KEY', 'cocacola'); // Change ça par une vraie clé secrète
}

if (!defined('SECRET_IV')) {
    define('SECRET_IV', 'pnl'); // Un IV pour renforcer la sécurité
}


if (!function_exists('encryptEmail')) {
    function encryptEmail($email) {
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        return openssl_encrypt($email, 'AES-256-CBC', $key, 0, $iv);
    }
}

if (!function_exists('decryptEmail')) {
    function decryptEmail($encryptedEmail) {
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        return openssl_decrypt($encryptedEmail, 'AES-256-CBC', $key, 0, $iv);
    }
}
?>
