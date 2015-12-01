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
function putData($riak, $location, $object) {

  $storeCommand = (new Command\Builder\StoreObject($riak))
                      ->buildObject($object)
                      ->atLocation($location)
                      ->build();
  $storeCommand->execute();

}

// === Fetch objects ===
// TODO: Return values
function getData($riak, $location) {
  $fetchCommand = (new Command\Builder\FetchObject($riak))
  			->atLocation($location)
  			->build()
        ->execute()
        ->getObject()
        ->getData();
        
  return $fetchCommand;
}

// == Update objects ==
// TODO: Create an update function to update objects based from keys


// === Delete object (using key) ===
// This function also works for advanced bucket types
// TODO: Return values
function deleteData($riak, $location) {
  $deleteCommand = (new Command\Builder\DeleteObject($riak))
  			->atLocation($location)
  			->build();
  $deleteCommand->execute();
}



?>
