<?php


namespace microfw\core;


use Exception;

/**
 * Контейнер приложения
 */
class App
{

    /**
     * Хранит в себе экземпляр класса Registry
     * @var object
     */
    public static $app;

    /**
     * Запускает приложение
     * @throws Exception
     */
    public function __construct()
    {
        // Запуск обработчика ошибок
        new ErrorHandler();

        self::$app = Registry::instance();

        $router = new Router();

        // Пользовательские маршруты должны быть указаны выше основных

        // Основные маршруты

        $pattern = "(?P<controller>[a-z-]+)\/?(?P<action>[a-z-]+)?\/?(?P<alias>[0-9-a-z-]+)?";

        $router->addRoute('^api/?$', ['controller' => 'api', 'prefix' => 'api']);
        $router->addRoute("^api/?(?P<controller>[a-z-]+)\/?(?P<alias>[0-9-a-z-]+)?$", ['prefix' => 'api']);

        $router->addRoute('^a-n/?$', ['controller' => 'main', 'prefix' => 'admin']);
        $router->addRoute("^a-n/?$pattern$", ['prefix' => 'admin']);

        $router->addRoute('^$', ['controller' => 'main', 'action' => 'index']);
        $router->addRoute("^$pattern$");

        // Запуск маршрутизатора
        $router->dispatch();
    }
}