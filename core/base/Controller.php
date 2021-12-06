<?php


namespace microfw\core\base;


use microfw\core\base\View;

/**
 * Базовый контроллер
 */
abstract class Controller
{
    /**
     * Текущий маршрут
     * @var array
     */
    public array $route = [];

    /**
     * Текущий вид.
     * При переопределении позволяет переключить на пользовательский вид.
     * При переопределении в false - отключает вид
     * @var string
     */
    public string $view = '';

    /**
     * Текущий шаблон.
     * При переопределении позволяет переключить на пользовательский шаблон.
     * При переопределении в false - отключает шаблон
     * @var string
     */
    public string $layout = '';

    /**
     * Передаваемые во view данные для отображения
     * @var array
     */
    private array $data = [];

    public function __construct(array $route)
    {
        $this->route = $route;
        $this->view = $route['action'];
    }

    /**
     * Запуск отрисовки страницы
     */
    public function getView(): void
    {
        $objView = new View($this->route, $this->view, $this->layout);
        $objView->render($this->data);
    }

    /**
     * Добавление отображаемых данных
     * @param $arr
     */
    public function setData(array $arr): void
    {
        $this->data = $arr;
    }

    /**
     * Проверка на ajax запрос
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Подключение только вида
     * @param string $view
     * @param array $data
     */
    public function loadView(string $view, array $data = []): void
    {
        extract($data);
        if (isset($this->route['prefix'])){
            require APP . "/views/{$this->route['prefix']}/$view.php";
        } else {
            require APP . "/views/{$this->route['controller']}/$view.php";
        }
    }
}