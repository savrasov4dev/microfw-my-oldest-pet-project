<?php


namespace microfw\core\base;


use Exception;

class View
{

    /**
     * Текущий маршрут
     * @var array
     */
    private $route;

    /**
     * Подключаемый вид
     * @var string
     */
    private $view;

    /**
     * Подключаемый шаблон
     * @var string
     */
    private $layout;

    /**
     * Скрипты из подключаемого вида
     * @var array
     */
    private $scripts = [];

    /**
     * Значения мета тегов для подключаемого шаблона
     * @var array
     */
    private $meta = [];

    /**
     * View constructor.
     * @param $route
     * @param $view
     * @param $layout
     */
    public function __construct( $route, $view, $layout )
    {
        $this->route = $route;
        $this->view = $view;
        $this->layout = $layout ?: LAYOUT;
    }

    /**
     * Принимает в себя данные из $data и отрисовывает страницу
     * @param $data
     * @throws Exception
     */
    public function render($data): void
    {
        if ( is_array($data) ) extract($data);
        if ( isset($meta) and is_array($meta)) {
            $this->setMeta($meta['title'], $meta['description'], $meta['keywords']);
        }

        if (isset($this->route['prefix'])){
            $file_view = APP . "/views/{$this->route['prefix']}/$this->view.php";
        } else {
            $file_view = APP . "/views/{$this->route['controller']}/$this->view.php";
        }


        ob_start();

        if ( file_exists($file_view) ) {
            require_once $file_view;
        } else {
            throw new Exception($file_view . " не найден", 404);
        }

        $content = ob_get_clean();

        $file_layout = APP . "/views/layouts/$this->layout.php";

        if ( file_exists($file_layout) ) {
            $content = $this->getScripts($content);
            require_once $file_layout;
        } else {
            throw new Exception($file_layout . " не найден", 404);
        }
    }

    /**
     * Вырезает уникальные скрипты из view и сохраняет их в свойстве $scripts
     * для их последующей вставке в layout сразу после подключенных скриптовых библиотек.
     * @param $content
     * @return string
     */
    private function getScripts($content): string
    {
        $pattern = "#<script.*?>.*?</script>#si";
        preg_match_all($pattern, $content, $this->scripts);
        $this->scripts = $this->scripts[0];
        if (!empty($this->scripts)){
            $content = preg_replace($pattern, '', $content);
        }
        return $content;
    }

    /**
     * Добавляет значения для мета тегов в свойство $meta
     * @param string $title
     * @param string $description
     * @param string $keywords
     */
    public function setMeta(string $title = '', string $description = '', string $keywords = ''): void
    {
        $this->meta = [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }

    /**
     * Возвращает строку мета-тегов: title, description, keywords
     * @return string
     */
    public function getMeta(): string
    {
        $meta = '';
        if (isset($this->meta['title'])){
            $meta .= '<title>' . $this->meta['title'] . '</title>';
        }
        if (isset($this->meta['description'])){
            $meta .= '<meta name="description" content="' . $this->meta['description'] . '">';
        }
        if (isset($this->meta['keywords'])){
            $meta .= '<meta name="keywords" content="' . $this->meta['keywords'] . '">';
        }
        return $meta;
    }

}