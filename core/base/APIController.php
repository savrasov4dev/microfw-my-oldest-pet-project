<?php

namespace microfw\core\base;

use microfw\libs\components\Debug;

/**
 * Базовый класс обработки api запросов.
 * Заголовки и методы можно переопределить
 * в дочерних классах
 */
abstract class APIController
{
    /**
     * Header: AccessControlAllowOrigin
     * @var string $accessOrigin
     */
    public string $accessOrigin = '*';

    /**
     * Header: AccessControlAllowHeaders
     * @var string $accessHeaders
     */
    public string $accessHeaders = '*';

    /**
     * Header: AccessControlAllowMethods
     * @var string $accessMethods
     */
    public string $accessMethods = '*';

    /**
     * Header: AccessControlAllowCredentials
     * @var string $accessCredentials
     */
    public string $accessCredentials = '*';

    /**
     * Header: ContentType
     * @var string $type
     */
    public string $type = 'application/json';

    /**
     * Request Method
     * @var string $method
     */
    public string $method;

    /**
     * Table request method properties
     * @var array $methods
     */
    public array $methods = [
        'GET' => [
            'name' => 'read',
            'formData' => false,
            'rowData' => false,
            ],
        'POST' => [
            'name' => 'create',
            'formData' => true,
            'rowData' => false,
            ],
        'PATCH' => [
            'name' => 'update',
            'formData' => false,
            'rowData' => true,
        ],
        'DELETE' => [
            'name' => 'delete',
            'formData' => false,
            'rowData' => false,
        ],
    ];

    /**
     * Model
     * @var Model $model
     */
    public Model $model;

    /**
     * @param array $route
     * @throws \Exception
     */
    public function __construct(array $route)
    {
        header("Access-Control-Allow-Origin: $this->accessOrigin");
        header("Access-Control-Allow-Headers: $this->accessHeaders");
        header("Access-Control-Allow-Methods: $this->accessMethods");
        header("Access-Control-Allow-Credentials: $this->accessCredentials");
        header("Content-Type: $this->type; charset=UTF-8");

        $this->method = $_SERVER['REQUEST_METHOD'];

        $model = '\microfw\app\models\\' . ucfirst($route['controller']);

        $this->model = new $model();

        $id = $route['alias'] ?? false;

        $this->dispatch($id);
    }


    /**
     * Определяет каким методом обрабатывать запрос
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    private function dispatch(int $id): bool
    {
        $methodProperties = $this->findMethodProperties();

        // Проверка существования обработки текущего метода запроса
        if (!empty($methodProperties)) {

            $method = $methodProperties['name'] ?? false;

            // Проверка необходимости передачи данных в метод
            if (!$methodProperties['formData'] && !$methodProperties['rowData']) {

                $this->$method($id);
                return true;

            } else {
                if ($methodProperties['formData']) {

                    $data = $_POST['data'] ?? [];

                    // Проверка передачи данных в метод
                    if (!empty($data)) {

                        $this->$method($data);
                        return true;
                    }
                } else {

                    $data = json_decode(file_get_contents("php://input"), true);

                    // Проверка передачи данных в метод
                    if (!empty($data)) {

                        $this->$method($id, $data);
                        return true;
                    }
                }
                $this->displayResponse(400, false, 'Ошибка. Данные не переданы');
                return false;
            }
        } else {
            throw new \Exception("Request method not found", 404);
        }
    }

    /**
     * Ищет свойства текущего метода запроса
     * @return array
     */
    private function findMethodProperties(): array
    {
        $properties = $this->methods[$this->method] ?? [];
        return $properties;
    }

    /**
     * Добавляет в обработку метод запроса
     * @param string $requestMethodName
     * @param string $methodName
     * @param bool $formData
     * @param bool $rowData
     */
    public function addRequestMethod(
        string $requestMethodName,
        string $methodName,
        bool $formData,
        bool $rowData
    ): void
    {
        $this->methods[$requestMethodName] = [
            'name' => $methodName,
            'formData' => $formData,
            'rowData' => $rowData,
        ];
    }

    /**
     * Отключает обрабатываемый метод запроса
     * @param string $requestMethodName
     * @return bool
     */
    public function offRequestMethod(string $requestMethodName): bool
    {
        if (isset($this->methods[$requestMethodName])) {
            $this->methods[$requestMethodName] = [];
            return true;
        }
        return false;
    }

    /**
     * Отображает ответ на запрос
     * @param int $statusCode
     * @param bool $status
     * @param string|array $message
     */
    private function displayResponse(int $statusCode, bool $status, string|array $message): void
    {
        http_response_code($statusCode);
        $response = ['status' => $status, 'message' => $message];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Добавляет запись
     * @param array $data
     */
    public function create(array $data): void
    {
        $fields = [];
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = $value;
        }

        if (isset($fields) && isset($values)) {

            $result = $this->model->addOne($fields, $values);

            if ($result) {

                $this->displayResponse(200, true, 'Запись добавлена');

            } else {

                $this->displayResponse(400, true, 'Ошибка. Указаны неверные данные');
            }
        } else {

            $this->displayResponse(400, true, 'Ошибка. Данные неполные');
        }
    }

    /**
     * Чтение записи/записей
     * @param int|null $id
     */
    public function read(int $id = null): void
    {
        if ($id) {

            $result = $this->model->findOne($id);

            $message = 'Записи с таким id не существует';

        } else {

            $result = $this->model->findAll();

            $message = 'В таблице отсутвуют записи';
        }

        if (!empty($result)) {

            $this->displayResponse(200, true, $result);

        } else {

            $this->displayResponse(404, false, $message);
        }
    }


    /**
     * Обнавляет запись
     * @param array $data
     */
    public function update(array $data): void
    {
        if (isset($data['set']) && isset($data['condition'])) {

            $result = $this->model->updateOne($data['set'], $data['condition']);

            if ($result) {

                $this->displayResponse(200, true, 'Запись изменена');

            } else {

                $this->displayResponse(400, true, 'Ошибка. Указаны неверные данные');
            }
        } else {

            $this->displayResponse(400, true, 'Ошибка. Данные неполные');
        }
    }

    /**
     * Удаляет запись
     * @param int $id
     */
    public function delete(int $id): void
    {
        if ($id) {

            $result = $this->model->deleteOne($id);

            if ($result) {

                $this->displayResponse(200, true, 'Запись успешно удалена');
            } else {

                $message = 'Запись с таким id не существует';
            }
        } else {

            $message = 'Ошибка. Введите id записи';
        }
        if (isset($message)) {

            $this->displayResponse(404, false, $message);
        }
    }
}