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
        $router->addRoute('^api/?$', ['controller' => 'api', 'prefix' => 'api']);
        $router->addRoute('^api/?(?P<controller>[a-z-]+)\/?(?P<action>[a-z-]+)?$', ['prefix' => 'api']);

        $router->addRoute('^a-n/?$', ['controller' => 'main', 'prefix' => 'admin']);
        $router->addRoute('^a-n/?(?P<controller>[a-z-]+)?/?(?P<action>[a-z-]+)?$', ['prefix' => 'admin']);

        $router->addRoute('^$', ['controller' => 'main', 'action' => 'index']);
        $router->addRoute('^(?P<controller>[a-z-]+)\/?(?P<action>[a-z-]+)?$');

        // Запуск маршрутизатора
        $router->dispatch();
    }
}