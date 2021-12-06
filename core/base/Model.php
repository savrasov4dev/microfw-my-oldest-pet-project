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
     * @var Db $pdo
     */
    private Db $pdo;

    /**
     * Текущая таблица
     * @var string $table
     */
    public string $table;

    /**
     * Первичный ключ по умолчанию
     * @var string $pk
     */
    public string $pk = 'id';

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
     * @param int $limit
     * @return array
     */
    public function findAll(int $limit = null): array
    {
        $sql = "SELECT * FROM $this->table";
        if (!is_null($limit)) {
            $sql .= " LIMIT $limit";
        }
        return $this->pdo->query($sql);
    }

    /**
     * Нахождение одного элемента по номеру в искомом поле (первичный ключ по умолчанию)
     * @param int $id
     * @param string $field
     * @return array
     */
    public function findOne(int $id, string $field = null): array
    {
        $field = $field ?? $this->pk;
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
    public function findByLike(string $str, string $field, string $table = null): array
    {
        $str = "%{$str}%";
        $table = $table ?? $this->table;
        $sql = "SELECT * FROM $table WHERE ? LIKE ?;";
        return $this->pdo->query($sql, [$field, $str]);
    }

    /**
     * Выборка элементов по sql запросу
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function findBySql(string $sql, array $params = []): array
    {
        return $this->pdo->query($sql, $params);
    }

    /**
     * Добавление элементов по sql запросу
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function addBySql(string $sql, array $params = []): bool
    {
        return $this->pdo->execute($sql, $params);
    }

    /**
     * Обновляет элемент таблицы по условию.
     * По умолчанию стоит текущая таблица.
     * Пример записи:
     * updateOne(['field1' => 'value1', 'field2' => 'value2'], ['id' => 123])
     *
     * @param array $set
     * @param array $condition
     * @param string $table
     * @return bool
     */
    public function updateOne(array $set, array $condition, string $table = null): bool
    {
        $table = $table ?? $this->table;

        $fieldsSet  = array_keys($set);
        $fieldСondition = array_keys($condition);

        $params = array_values($set);
        foreach ($condition as $value ) { $params[] = $value; }

        $str = '';
        foreach ( $fieldsSet as $field) {
            $str .= "`$field` = ? , ";
        }   $str = preg_replace("~, $~", " ", $str);

        $sql = "UPDATE $table SET $str WHERE `$fieldСondition[0]`= ? ;";
        return $this->pdo->execute($sql, $params);
    }

    /**
     * Добавляет элемент в таблицу.
     * По умолчанию стоит текущая таблица.
     * Пример записи:
     * addOne(['field1', 'field2'], ['value1', 'value2])
     * @param array $fields
     * @param array $values
     * @param string $table
     * @return bool
     */
    public function addOne(array $fields, array $values, string $table = null): bool
    {
        $table = $table ?? $this->table;

        $strFields = '';

        foreach ( $fields as $field) {$strFields .= "`$field`,";}
        // Удаление лишней запятой
        $strFields = preg_replace("~,$~", "", $strFields);

        $strValues = str_repeat('?,', count($values));
        // Удаление лишней запятой
        $strValues = preg_replace("~,$~", "", $strValues);

        $sql = "INSERT INTO $table ($strFields) VALUES ($strValues)";
        return $this->pdo->execute($sql, $values);
    }

    /**
     * Удаление элемента по номеру в искомом поле (первичный ключ по умолчанию)
     * @param int $id
     * @param string $field
     * @return bool
     */
    public function deleteOne(int $id, string $field = null): bool
    {
        $field = $field ?? $this->pk;
        $sql = "DELETE FROM $this->table WHERE $field = ? LIMIT 1;";
        return $this->pdo->execute($sql, [$id]);
    }
}