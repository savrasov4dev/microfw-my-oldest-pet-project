<?php

namespace microfw\core;


/**
 * Добавляет в глобальную область видимости сервисы приложения
 */
class Registry
{
    use TraitSingleton;

    /**
     * Используемые сервисы
     * @var array
     */
    private static $services = [];

    /**
     * @var array
     */
    private static $config = [];

    /**
     * Подключает сервисы из конфиг файла
     */
    private function __construct()
    {
        self::$config = require CONFIG . '/config.php';
        foreach ( self::$config['services'] as $name => $service) {
            self::$services[$name] = new $service;
        }
    }

    /**
     * @param $service
     * @return object|string
     */
    public function __get($service)
    {
        if ( !is_object(self::$services[$service]) ) {
            return "Invalid key given";
        }
        return self::$services[$service];
    }

    /**
     * Добавляет сервис
     * @param $name
     * @param $service
     * @return void
     */
    public function __set($name, $service): void
    {
        if ( !isset(self::$services[$name]) ) {
            self::$services[$name] = new $service;
        }
    }

    /**
     * Список добавленных сервисов
     * @return array
     */
    public static function getList(): array
    {
        return self::$services;
    }
}