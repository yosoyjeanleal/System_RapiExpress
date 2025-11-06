<?php
use RapiExpress\Helpers\Lang;

function lang_cambiar() {
    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        // Guardar en cookie
        setcookie('selectedLang', $lang, time() + 365*24*60*60, '/');
    }

    // Redirigir a la página anterior o al login
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: $redirect");
    exit;
}
