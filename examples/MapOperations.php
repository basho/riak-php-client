<?PHP
/*
Project name: Riak Simple PHP Functions Basic Operations
Created by: Allan Paul Sy Ortile

An important note on maps:

  Maps in itself is a different type of beast. If you're familiar with abstract data types, that is the easiest way to explain it. A map could contain any one or more of the following: counters, sets, and another map. It is further explained here: https://docs.basho.com/riak/latest/dev/using/data-types/

  Some maps only functions exists like registers and flags. The others are counters, sets and map itself.

*/

// This requires the absolute path to the autoload.php file from the riak-php-client installation
require __DIR__ . '/../vendor/autoload.php';

// This includes a couple of commonly used libraries
use Basho\Riak;
use Basho\Riak\Location;
use Basho\Riak\Node;
use Basho\Riak\Command;
use Basho\Riak\Bucket;

// == REGISTER FUNCTIONS START ==
// TODO: Return values
// As a sidenote, a register is a string. :p
function putMapDataFlag($riak, $location, $flagName, $flagValue) {
  $response =  (new \Basho\Riak\Command\Builder\UpdateMap($riak))
      ->updateFlag($flagName, $flagValue)
      ->atLocation($location)
      ->build()
      ->execute();
}

// TODO: Return values
function getMapDataFlag($riak, $location, $flagName) {
  $map = (new \Basho\Riak\Command\Builder\FetchMap($riak))
    ->atLocation($location)
    ->build()
    ->execute()
    ->getMap();

  return $map->getFlag($flagName);
}
// == REGISTER FUNCTIONS END ==

// == REGISTER FUNCTIONS START ==
// TODO: Return values
// As a sidenote, a register is a string. :p
function putMapDataRegister($riak, $location, $registerName, $registerValue) {
  // Creating/updating a map with a register
  $response =  (new \Basho\Riak\Command\Builder\UpdateMap($riak))
      ->updateRegister($registerName, $registerValue)
      ->atLocation($location)
      ->build()
      ->execute();
}

// TODO: Return values
function getMapDataRegister($riak, $location, $registerName) {
  $map = (new \Basho\Riak\Command\Builder\FetchMap($riak))
    ->atLocation($location)
    ->build()
    ->execute()
    ->getMap();

  return ($map->getRegister($registerName));
}
// == REGISTER FUNCTIONS END ==

// == COUNTER FUNCTIONS START ==
// TODO: Return values
function putMapDataCounter($riak, $location, $counterName, $counterIncrementValue) {
  $counter_builder = (new \Basho\Riak\Command\Builder\IncrementCounter($riak))
      ->withIncrement($counterIncrementValue);

  $create_update_counter = (new \Basho\Riak\Command\Builder\UpdateMap($riak))
      ->updateCounter($counterName, $counter_builder)
      ->atLocation($location)
      ->build();

  $create_update_counter->execute();

}

// TODO: Return values
function getMapDataCounter($riak, $location, $counterName) {
  $map = (new \Basho\Riak\Command\Builder\FetchMap($riak))
    ->atLocation($location)
    ->build()
    ->execute()
    ->getMap();

  // Returns an integer but typecasting to a string for output
  return $map->getCounter($counterName)->getData();
}
// == COUNTER FUNCTIONS END ==

// == SETS FUNCTIONS START ==
// TODO: Could this be an array from the get go instead of iterating?
function putMapDataSets($riak, $location, $setName, $setObjects) {

  foreach ($setObjects as $setObject) {
    $set_builder = (new \Basho\Riak\Command\Builder\UpdateSet($riak))
        ->add($setObject);

    $create_update_sets = (new \Basho\Riak\Command\Builder\UpdateMap($riak))
        ->updateSet($setName, $set_builder)
        ->atLocation($location)
        ->build();

    $create_update_sets->execute();
  }

}

function getMapDataSets($riak, $location, $setName) {
  $map = (new \Basho\Riak\Command\Builder\FetchMap($riak))
    ->atLocation($location)
    ->build()
    ->execute()
    ->getMap();

  // Returns an array
  return ($map->getSet($setName)->getData());

}
// == SETS FUNCTIONS END ==


// MAP FUNCTIONS START
// This is the type of recursion where people tend to shoot themselves at the foot. Myself included.
//  I will figure out how to properly recurse through map values maybe having a map variable passed here would be one of the solution
function putMapDataMap() {
}

function getMapDataMap() {
}
// MAP FUNCTIONS END

//  == var_dump for debug purposes only ==
function getMapVarDump ($riak, $location) {
  $map = (new \Basho\Riak\Command\Builder\FetchMap($riak))
    ->atLocation($location)
    ->build()
    ->execute()
    ->getMap();

  // For debugging purposes only, var dumping the whole map
  return var_dump($map);
}

/* DELETING STUFF
Delete set is just recycled from the basic operations. Same method to delete via location
*/

?>
