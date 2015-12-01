<?PHP
/*
Project name: Riak Simple PHP Functions Basic Operations
Created by: Allan Paul Sy Ortile

*/

// This requires the absolute path to the autoload.php file from the riak-php-client installation
require __DIR__ . '/../vendor/autoload.php';

// This includes a couple of commonly used libraries
use Basho\Riak;
use Basho\Riak\Location;
use Basho\Riak\Node;
use Basho\Riak\Command;
use Basho\Riak\Bucket;

// === Store or add data ===
// TODO: Return values
function putCounterData($riak, $location, $counterIncrementValue) {

  $storeIncrement = (new \Basho\Riak\Command\Builder\IncrementCounter($riak))
      ->withIncrement($counterIncrementValue)
      ->atLocation($location)
      ->build()
      ->execute();

}

// TODO: Return values
function getCounterData($riak, $location) {

  $fetchIncrement = (new \Basho\Riak\Command\Builder\FetchCounter($riak))
      ->atLocation($location)
      ->build()
      ->execute()
      ->getCounter();

  // Returns an array
  return($fetchIncrement->getData());

}

/* DELETING STUFF
Delete set is just recycled from the basic operations. Same method to delete via location
*/

?>
