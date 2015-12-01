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
// TODO: Could I just put in the array values as a set as a whole?
// The funciton accepts the object as an array and iterates and adds the array contents
function putSetData($riak, $location, $setObjects) {

  foreach ($setObjects as $value) {
    //echo $value . "\n";
    $response = (new \Basho\Riak\Command\Builder\UpdateSet($riak))
        ->add($value)
        ->atLocation($location)
        ->withParameter('returnbody', 'true')
        ->build()
        ->execute();
  }

}

// TODO: Return values
// TODO: Could I just get the values as an array?
function getSetData($riak, $location) {

  $set = (new \Basho\Riak\Command\Builder\FetchSet($riak))
      ->atLocation($location)
      ->build()
      ->execute()
      ->getSet();

  // Returns an array
  return($set->getData());

}

/* DELETING STUFF
Delete set is just recycled from the basic operations. Same method to delete via location
*/


?>
