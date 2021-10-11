<?php

namespace microfw\core;


use PDO;

/**
 * Выполняет подключение и базовые запросы к БД
 */
class Db
{
    use TraitSingleton;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Количество сделанных запросов
     * @var int
     */
    public static $countSql = 0;

    /**
     * Список сделанных запросов
     * @var array
     */
    public static $queries = [];


    private function __construct()
    {
        $db = require CONFIG . "/config_db.php";
        $this->pdo = new PDO($db['dsn'], $db['user'], $db['pass'], $db['options']);
    }

    /**
     * Выполняет sql запрос без возвращения данных
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function execute(string $sql, array $params = []): bool
    {
        self::setLogSql($sql);

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Выполняет sql запрос, предполагающий выборку данных
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        $this->setLogSql($sql);

        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute($params);
        if ( $res !== false ) {
            return $stmt->fetchAll();
        }
        return [];
    }

    /**
     * Логирует все sql запросы.
     * Может понадобиться при отладке SQL запросов
     * @param $sql
     */
    private static function setLogSql($sql): void
    {
        self::$countSql++;
        self::$queries[] = $sql;
    }

    /**
     * Возвращает логи sql запросов
     * @return array
     */
    public static function getLogSql(): array
    {
        return [
            'CountSql' => self::$countSql,
            'queries'  => self::$queries,
        ];
    }


}