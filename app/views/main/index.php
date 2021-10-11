<h2>microfw - micro framework</h2>
<p>
    Небольшой фреймворк, в основе которого лежит MVC архитектура. <br>
    Http запросы перенаправляются на Front controller в public/index.php, <br>
    в котором инициируется запуск приложения командой new App.
</p>
<p>
    В конструкторе класса microfw\core\App добавляются правила перенаправления, <br>
    после чего запускается обработчик Http запросов. <br> <br>
    Пример работы обработчика: <br>
    запрос: http://domen-name./main/index вызовет MainController->indexAction() <br>
    Controller по умолчанию: MainController <br>
    Action по умолчанию: indexAction
</p>
<p>
    Для подключения БД нужно настроить файл config_db.php. <br>
    Параметры переопределяются в моделях в папке app/model/
</p>
<p>
    Виды расположены в app/views/<controller_name>/<action_name>.php <br>
    В приложении по умолчанию реализовано подключение видов и шаблона. <br>
    Для его отключения в контроллере следует переопределить свойство view в false, <br>
    а для шаблона переопределить свойство layout в false
</p>




