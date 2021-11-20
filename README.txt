microfw - micro framework

Небольшой фреймворк, в основе которого лежит MVC архитектура.
Http запросы перенаправляются на Front controller в public/index.php,
в котором инициируется запуск приложения командой new App.

В конструкторе класса microfw\core\App добавляются правила перенаправления,
после чего запускается обработчик Http запросов.
Пример работы обработчика:
    запрос: http://domen-name./main/index вызовет MainController->indexAction()
    Controller по умолчанию: MainController
    Action по умолчанию: indexAction

Для работы с microfw нужно перекинуть .htaccess из корня mickrofw в корень проекта,
заменить в файле строку RewriteRule ^(.*)$ /public/$1 на RewriteRule ^(.*)$ /microfw/public/$1,
после чего подключить БД

Для подключения БД нужно настроить файл config_db.php.
Параметры переопределяются в моделях в папке app/model/

Виды расположены в app/views/<controller_name>/<action_name>.php
В приложении по умолчанию реализовано подключение видов и шаблона.
Для его отключения в контроллере следует переопределить свойство view в false,
а для шаблона переопределить свойство layout в false