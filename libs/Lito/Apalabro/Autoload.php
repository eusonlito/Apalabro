<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Autoload {
    static private $classes = array();
    static private $namespaces = array();

    /**
     * static public function register ()
     *
     * Installs this class loader on the SPL autoload stack.
     */
    static public function register ()
    {
        spl_autoload_register(__NAMESPACE__.'\\Autoload::autoload');
    }

    /**
     * static public function unregister ()
     *
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    static public function unregister ()
    {
        spl_autoload_unregister(__NAMESPACE__.'\\Autoload::autoload');
    }

    /**
     * static public function autoload ($class)
     *
     * Basic autoload function
     * Returns boolean
     */
    static public function autoload ($class)
    {
        $class = ltrim($class, '\\');

        if (isset(self::$classes[$class]) && is_file(self::$classes[$class])) {
            return require (self::$classes[$class]);
        }

        $file  = '';
        $namespace = '';

        if ($lastNsPos = strripos($class, '\\')) {
            $namespace = substr($class, 0, $lastNsPos);
            $class = substr($class, $lastNsPos + 1);
            $file  = preg_replace('#[\\\/]+#', '/', $namespace.'/');
        }

        $file .= $class.'.php';

        if (is_file(BASE_PATH.'libs/'.$file)) {
            return require (BASE_PATH.'libs/'.$file);
        } else {
            foreach (self::$namespaces as $ns => $path) {
                if (strpos($namespace, $ns) === 0) {
                    $namespace_file = preg_replace('#[\\\/]+#', '/', $path.preg_replace('#^'.preg_quote($ns).'#', '', $namespace).'/').basename($file);

                    if (is_file($namespace_file)) {
                        return require ($namespace_file);
                    }
                }
            }

            echo '<pre>';

            throw new \Exception(sprintf('File %s can not be loaded from libs folders (%s)', $file, BASE_PATH.'libs/'));
        }
    }

    /**
     * static public function registerClass (array $classes)
     * static public function registerClass (string $class, string $path)
     *
     * Sets a new path for an specific class
     * Returns none
     */
    static public function registerClass ($class, $path = null)
    {
        if (is_array($class)) {
            foreach ($class as $class => $path) {
                self::$classes[$class] = $path;
            }

            return;
        }

        self::$classes[$class] = $path;
    }

    /**
     * static public function registerNamespace (array $namespaces)
     * static public function registerNamespace (string $namespace, string $path)
     *
     * Sets a new base path for an specific namespace
     * Returns none
     */
    static public function registerNamespace ($namespace, $path = null)
    {
        if (is_array($namespace)) {
            foreach ($namespace as $namespace => $path) {
                self::$namespaces[$namespace] = $path;
            }

            return;
        }

        self::$namespaces[$namespace] = $path;
    }

    /**
     * static public function registerComposer ()
     *
     * Register the classes installed by composer
     * Returns none
     */
    static function registerComposer ()
    {
        $file = BASE_PATH.'libs/'.'composer/autoload_classmap.php';

        if (is_file($file)) {
            self::registerClass(include($file));
        }

        $file = BASE_PATH.'libs/composer/autoload_namespaces.php';

        if (is_file($file)) {
            foreach (include($file) as $namespace => $path) {
                self::registerNamespace($namespace, $path.'/'.$namespace.'/');
            }
        }
    }
}
