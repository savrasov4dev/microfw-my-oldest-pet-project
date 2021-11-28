<?php
// Включает/Отключает отладку ошибок.
// Для включения  - 1;
// Для выключения - 0;
const DEBUG = 1;

define("ROOT", dirname(__DIR__) );

const APP    = ROOT . "/app";
const CONFIG = ROOT . "/config";
const CORE   = ROOT . "/core";
const BASE   = ROOT . "/core/base";
const LIBS   = ROOT . "/libs";
const WWW    = ROOT . "/public";
const CACHE  = ROOT . "/tmp/cache";
const VENDOR = ROOT . "/vendor";

const LAYOUT = "default";

use microfw\core\App;
new App();