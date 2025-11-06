<?php
namespace RapiExpress\Helpers;

class Lang {
    private static $lang = 'es';
    private static $strings = [];

    /**
     * Inicializa el sistema de idiomas
     * Prioridad: Sesión > Cookie > Navegador > Default
     */
    public static function init($default = 'es') {
        // 1. Verificar si hay idioma en sesión (más confiable)
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['es', 'en'])) {
            self::$lang = $_SESSION['lang'];
        }
        // 2. Verificar parámetro GET (para cambio de idioma)
        elseif (isset($_GET['lang']) && in_array($_GET['lang'], ['es', 'en'])) {
            self::$lang = $_GET['lang'];
            $_SESSION['lang'] = self::$lang;
        }
        // 3. Verificar cookie
        elseif (isset($_COOKIE['selectedLang']) && in_array($_COOKIE['selectedLang'], ['es', 'en'])) {
            self::$lang = $_COOKIE['selectedLang'];
            $_SESSION['lang'] = self::$lang;
        }
        // 4. Detectar del navegador
        elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($browserLang, ['es', 'en'])) {
                self::$lang = $browserLang;
                $_SESSION['lang'] = self::$lang;
            } else {
                self::$lang = $default;
                $_SESSION['lang'] = self::$lang;
            }
        }
        // 5. Default
        else {
            self::$lang = $default;
            $_SESSION['lang'] = self::$lang;
        }

        // Guardar en cookie para persistencia (1 año)
        setcookie('selectedLang', self::$lang, time() + 365*24*60*60, '/', '', false, true);

        // Cargar archivo de idioma
        $langFile = __DIR__ . '/../lang/' . self::$lang . '.php';
        if (file_exists($langFile)) {
            self::$strings = include $langFile;
        } else {
            // Fallback a español si no existe el archivo
            self::$strings = include __DIR__ . '/../lang/es.php';
        }
    }

    /**
     * Obtiene una cadena traducida
     */
    public static function get($key, $default = null) {
        return self::$strings[$key] ?? $default ?? $key;
    }

    /**
     * Obtiene el idioma actual
     */
    public static function current() {
        return self::$lang;
    }

    /**
     * Cambia el idioma actual
     */
    public static function change($newLang) {
        if (in_array($newLang, ['es', 'en'])) {
            self::$lang = $newLang;
            $_SESSION['lang'] = $newLang;
            setcookie('selectedLang', $newLang, time() + 365*24*60*60, '/', '', false, true);
            self::init();
            return true;
        }
        return false;
    }

    /**
     * Verifica si un idioma es válido
     */
    public static function isValid($lang) {
        return in_array($lang, ['es', 'en']);
    }

    /**
     * Obtiene todos los idiomas disponibles
     */
    public static function available() {
        return [
            'es' => ['name' => 'Español', 'flag' => '🇪🇸'],
            'en' => ['name' => 'English', 'flag' => '🇺🇸']
        ];
    }
}