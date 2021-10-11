<?php


namespace microfw\libs\components;


use function microfw\libs\functions\debug;

/**
 * Класс кеширования
 */
class Cache
{

    /**
     * Кеширует данные по ключу
     * @param $key
     * @param $data
     * @param int $time
     * @return bool
     */
    public function set($key, $data, $time = 60*60)
    {
        $content['data'] = $data;
        $content['end_time'] = time() + $time;
        $file = CACHE . "/" . md5($key) .".txt";
        if (file_put_contents($file, serialize($content))) {
            return true;
        }   return false;
    }

    /**
     * Достает данные из кеша
     * @param $key
     * @return false|mixed
     */
    public function get($key)
    {
        $file = CACHE . "/" . md5($key) .".txt";
        if (file_exists($file)) {
            $content = unserialize(file_get_contents($file));
            if ($content['end_time'] >= time()) {
                return $content['data'];
            }
        }   return false;
    }

    /**
     * Удаляет файл кеша по ключу
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        $file = CACHE . "/" . md5($key) .".txt";
        if (file_exists($file)) {
            unlink($file);
            return true;
        }   return false;
    }

}