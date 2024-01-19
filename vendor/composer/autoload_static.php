<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc45a13da5b2355b1f471c5ebbca2787a
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'Erick\\PetShop\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Erick\\PetShop\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc45a13da5b2355b1f471c5ebbca2787a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc45a13da5b2355b1f471c5ebbca2787a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc45a13da5b2355b1f471c5ebbca2787a::$classMap;

        }, null, ClassLoader::class);
    }
}
