<?php

namespace microfw\core\base;

use microfw\core\Db;

/**
 * Базовый класс модели
 */
abstract class Model
{
    /**
     * Хранит обЪект подключения к БД
     * @var object
     */
    private $pdo;

    /**
     * Текущая таблица
     * @var string
     */
    public $table;

    /**
     * Первичный ключ по умолчанию
     * @var string
     */
    public $pk = 'id';

    /**
     * Подключает базу данных
     */
    public function __construct()
    {
        $this->pdo = Db::instance();
    }

    /**
     * Возвращает логи sql запросов
     * @return array
     */
    public function getLogSql(): array
    {
        return Db::getLogSql();
    }

    /**
     * Выборка всех данных из таблицы
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM $this->table";
        return $this->pdo->query($sql);
    }

    /**
     * Нахождение одного элемента по номеру в искомом поле (первичный ключ по умолчанию)
     * @param int $id
     * @param string $field
     * @return array
     */
    public function findOne(int $id, string $field = ''): array
    {
        $field = $field ?: $this->pk;
        $sql = "SELECT * FROM $this->table WHERE $field = ? LIMIT 1;";
        $result = $this->pdo->query($sql, [$id]);
        if (isset($result[0])) {
            return $result;
        }
        return [];
    }

    /**
     * Нахождение элементов по совпадению со строкой
     * @param string $str
     * @param string $field
     * @param string $table
     * @return array
     */
    public function findByLike(string $str, string $field, string $table = ''): array
    {
        $str = "%{$str}%";
        $table = $table ?: $this->table;
        $sql = "SELECT * FROM $table WHERE ? LIKE ?;";
        return $this->pdo->query($sql, [$field, $str]);
    }

    /**
     * Выборка элементов по sql запросу
     * @param $sql
     * @param array $params
     * @return array
     */
    public function findBySql($sql, array $params = []): array
    {
        return $this->pdo->query($sql, $params);
    }

    /**
     * Добавление элементов по sql запросу
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function addBySql($sql, array $params = []): bool
    {
        return $this->pdo->execute($sql, $params);
    }

    /**
     * Обновляет элемент таблицы по условию
     * По умолчанию стоит текущая таблица
     * Пример записи:
     * updateOne(['field1' => 'value1', 'field2' => 'value2'], ['id' => 123])
     *
     * @param array $set
     * @param array $where
     * @param string $table
     * @return bool
     */
    public function updateOne(array $set, array $where, string $table = ''): bool
    {
        $table = isset($table) ?: $this->table;

        $fieldsSet  = array_keys($set);
        $fieldWhere = array_keys($where);

        $params = array_values($set);
        foreach ( $where as $value ) { $params[] = $value; }

        $str = '';
        foreach ( $fieldsSet as $field) {
            $str .= "`$field` = ? , ";
        }   $str = preg_replace("~, $~", " ", $str);

        $sql = "UPDATE $table SET $str WHERE `$fieldWhere[0]`= ? ;";
        return $this->pdo->execute($sql, $params);
    }

    /**
     * Добавляет элемент в таблицу
     * По умолчанию стоит текущая таблица
     * Пример записи:
     * addOne(['field1', 'field2'], ['value1', 'value2])
     * @param array $fields
     * @param array $values
     * @param string $table
     * @return bool
     */
    public function addOne(array $fields, array $values, string $table = ''): bool
    {
        $table = $table ?? $this->table;

        $strFields = '';

        foreach ( $fields as $field) {$strFields .= "`$field` , ";}
        // Удаление лишней запятой
        $strFields = preg_replace("~, $~", " ", $strFields);

        $strValues = str_repeat('?, ', count($values));
        // Удаление лишней запятой
        $strValues = preg_replace("~, $~", "", $strValues);

        $sql = "INSERT INTO $table ( $strFields ) VALUES ( $strValues )";
        return $this->pdo->execute($sql, $values);
    }

    /**
     * Удаление элемента по номеру в искомом поле (первичный ключ по умолчанию)
     * @param int $id
     * @param string $field
     * @return bool
     */
    public function deleteOne(int $id, string $field = ''): bool
    {
        $field = isset($field) ?: $this->pk;
        $sql = "DELETE FROM $this->table WHERE $field = ? LIMIT 1;";
        return $this->pdo->execute($sql, [$id]);
    }
}