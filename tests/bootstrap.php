<?php
/**
 * Php-Riak-client test bootstrap file
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
if (false === spl_autoload_functions()) {
    if (function_exists('__autoload')) {
        spl_autoload_register('__autoload', false);
    }
}
date_default_timezone_set(@date_default_timezone_get());

define('HOST', 'localhost');
define('PORT', 8098);

spl_autoload_register(
function ($name)
{
    if ('Basho\Riak\\' == substr($name, 0, 11)) {
        $path = __DIR__ . '/../src' . DIRECTORY_SEPARATOR
                . str_replace('\\', DIRECTORY_SEPARATOR, $name)
                . '.php';
        require_once $path;
    }
});
