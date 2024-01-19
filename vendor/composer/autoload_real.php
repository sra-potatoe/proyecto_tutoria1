<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc45a13da5b2355b1f471c5ebbca2787a
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitc45a13da5b2355b1f471c5ebbca2787a', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc45a13da5b2355b1f471c5ebbca2787a', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitc45a13da5b2355b1f471c5ebbca2787a::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
