<?php

namespace microfw\libs\functions;

/**
 * Записывает из вхордящего массива в массив $intertwine поочередно ключ, значение:
 * arr('key' => 'value', 'key' => 'value' ):  intertwine('key', 'value', 'key', 'value')
 * @param array $arr
 * @param array $intertwine
 * @return array
 */
function intertwineArr(array $arr, array $intertwine = []) {
    foreach ( $arr as $key => $value ) {
        $intertwine[] = $key;
        $intertwine[] = $value;
    } return $intertwine;
}