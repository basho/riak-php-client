<?php
/**
* Php-Riak-client test bootstrap file
*
* PHP Version 5.3.*
*
* @author Johannes Skov Frandsen <jsf@greenoak.dk>
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
*/
if (false === spl_autoload_functions()) {
    if (function_exists('__autoload')) {
        spl_autoload_register('__autoload', false);
    }
}
require_once dirname(__FILE__).'/../src/Karla.php';
spl_autoload_register(array('Karla', 'autoload'));