<?php

namespace microfw\core;

use Throwable;

/**
 * Класс обработки ошибок
 */
class ErrorHandler
{
    /**
     * Запускает обработку ошибок
     */
    public function __construct()
    {
        if (DEBUG) {
            error_reporting(-1);
        } else {
            error_reporting(0);
        }

        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        ob_start();
        register_shutdown_function([$this, 'fatalErrorHandler']);
    }

    /**
     * Обрабатывает текущую ошибку
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return bool
     */
    public function errorHandler($errno, $errstr, $errfile, $errline): bool
    {
        $this->errorLog($errno, $errstr, $errfile, $errline);
        $this->displayError($errno, $errstr, $errfile, $errline);
        return true;
    }

    /**
     * Обрабатывает фатальные ошибки
     */
    public function fatalErrorHandler(): void
    {
        $error = error_get_last();
        if ($error && $error['type'] & ( E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            $this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
        } else {
            ob_end_flush();
        }
    }

    /**
     * Обрабатевает исключения
     * @param Throwable $e
     */
    public function exceptionHandler(Throwable $e): void
    {
        $code = $e->getCode();
        $code = ($code != 0) ?: 500;
        $this->errorLog("Exception", $e->getMessage(), $e->getFile(), $e->getLine());
        $this->displayError("Exception", $e->getMessage(), $e->getFile(), $e->getLine(), $code);
    }

    /**
     * Отоброжает текущую ошибку
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param int $response
     */
    private function displayError($errno, $errstr, $errfile, $errline, int $response = 500): void
    {
        http_response_code($response);
        if (DEBUG) {
            require APP . "/views/errors/dev.php";
        } else {
            require APP . "/views/errors/prod.php";
        }
        die;
    }

    /**
     * Логирует текущую ошибку
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    private function errorLog($errno, $errstr, $errfile, $errline): void
    {
        $messege = "\n\r[" . date("Y-m-d - H::i::s", time()) . "] Code: $errno || String: $errstr || File: $errfile || Line: $errline \n==============================================================================";
        $logFile = ROOT . "/tmp/logs/errors.log";
        error_log($messege, 3, $logFile);
    }
}