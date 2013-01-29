<?php
/**
 * The Riak API for PHP allows you to connect to a Riak instance,
 * create, modify, and delete Riak objects, add and remove links from
 * Riak objects, run Javascript (and
 * Erlang) based Map/Reduce operations, and run Linkwalking
 * operations.
 *
 * See the tests/riak/* files for example usage.
 * 
 * @author Rusty Klophaus <rusty@basho.com>
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * 
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
function autoload($className)
{
	require dirname(__FILE__).DIRECTORY_SEPARATOR.$className.'.php';
}

