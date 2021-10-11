<?php

namespace microfw\core;

use Exception;

/**
 * Пепенапряет запросы по установленным маршрутам
 */
class Router
{
    /**
     * Текущий маршрут
     * @var array
     */
    private $route = [];

    /**
     * Все добавленные шаблоны маршрутов
     * @var array
     */
    private $routes = [];

    /**
     * Добавляет шаблон маршрута
     * @param $regexp
     * @param array $route
     */
    public function addRoute($regexp, array $route = []): void
    {
       $this->routes[$regexp] = $route;
    }

    /**
     * Проверяет на совпадение с шаблонами текущий URL.
     * При совпадении добавляет в $route
     * @param $url
     * @return bool
     */
    private function matchRoute($url): bool
    {
        foreach ( $this->routes as $pattern => $route ) {
            if ( preg_match( "#$pattern#i", $url, $matches ) ) {

                // Записывает в текщий маршрут только значения с именованными ключами
                foreach ( $matches as $k => $v ) {
                    if ( is_string($k) ) { $route[$k] = $v; }
                }
                // Добавляет action по умолчанию
                if ( !isset($route['action']) ) {
                    $route['action'] = 'index';
                }
                $this->route = $route;
                return true;
            }
        }
        return false;
    }

    /**
     * Форматирует текст под upperCamelCase
     * @param $name
     * @return string
     */
    private static function upperCamelCase($name): string
    {
        $name = str_replace("-", " ", $name );
        $name = ucwords($name);
        return str_replace(" ", "", $name);
    }

    /**
     * Форматирует текст под lowerCamelCase
     * @param $name
     * @return string
     */
    private static function lowerCamelCase($name): string
    {
        return lcfirst(self::upperCamelCase($name));
    }

    /**
     * Подготавливает URL для дальнейшей обработки
     * @return string
     */
    private static function getUrl(): string
    {
        $url = ltrim($_SERVER['REQUEST_URI'], "/");
        if (preg_match("#^(.*)?(.*)$#", $url)){
            $url = explode('?', $url)[0];
        }
        return $url;
    }

    /**
     * Перенаправляет запрос на подходящий Controller и Action
     * с автоподключением вида страницы
     * @return bool
     * @throws Exception
     */
    public function dispatch(): bool
    {
        if ( $this->matchRoute(self::getUrl()) ) {

            $controller = self::upperCamelCase($this->route['controller']);

            if (isset($this->route['prefix'])) {
                $controller = "microfw\\app\\controllers\\{$this->route['prefix']}\\{$controller}Controller";
            } else {
                $controller = "microfw\\app\\controllers\\{$controller}Controller";
            }
            if ( class_exists( $controller ) ) {
                $cObj = new $controller($this->route);
                $action = self::lowerCamelCase($this->route['action']);
                $action = $action . 'Action';
                if ( method_exists($cObj, $action) ) {
                    $cObj->$action();
                    $cObj->getView();
                    return true;
                }
                throw new Exception("Not found: $action", 404);
            }
            throw new Exception("Not found: $controller", 404);
        }
        throw new Exception("Page not found", 404);
    }

    /**
     * Возвращает текущий маршрут
     * @return array
     */
    public function getRoute(): array
    {
        return $this->route;
    }

    /**
     * Возвращает все маршруты
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}