<?PHP
// -- Start Copy --
// This requires the absolute path to the autoload.php file from the riak-php-client installation
require __DIR__ . '/../vendor/autoload.php';

// This includes a couple of commonly used libraries
use Basho\Riak;
use Basho\Riak\Location;
use Basho\Riak\Node;
use Basho\Riak\Command;
use Basho\Riak\Bucket;

// Include directories for several commonly used Riak functions
// Make sure that the files are on the same directory as this file
// Comment out unneeded operation files
include ("BasicOperations.php");
// Remember that using custom bucket types needs you to do a riak-admin create bucket and activate
include ("SetOperations.php"); // Make sure you set a bucket type on $bucket
include ("CounterOperations.php"); // Make sure you set a bucket type on $bucket
include ("MapOperations.php"); // Make sure you set a bucket type on $bucket
include ("DeleteBucketOperations.php"); // Dont blow up your cluster

// Riak node initialization
// TODO: Convert this to a cluster setup
$node_name = 'riak@127.0.0.1';
$node_port = 10018;
$node = (new Node\Builder)
    ->atHost($node_name)
    ->onPort($node_port)
    ->build();

// Instatntiate the Riak node
$riak = new Riak([$node]);
//-- End Copy --

// == CONFIGURATION ==

// Bucket, key and object to be stored configuration. This will be passed on to the different functions later on
$bucket_name = "fruit_stand";
$bucket_type = "";
$key_name = "1";
// Bucket declares a new bucket (and type if applicable)
if (empty($bucket_type)) {
  // Use default bucket
  $bucket = new Riak\Bucket($bucket_name);
} else {
  // Use custom bucket type
  // add $bucket_type parameter for custom bucket types
  $bucket = new Riak\Bucket($bucket_name, $bucket_type);
}
// Location says where it will be placed, keys and bucket
$location = new Riak\Location($key_name, $bucket);

// == END CONFIGURATION ==

// == DECLARE YOUR VARIABLES HERE ==
$object = 'mango';

$flagName = "is_human";
$flagValue = true;

$registerName = "username";
$registerValue = "pogz";

$counterIncrementValue = 1;
$counterName = "pageviews";

$setName = "fruits";
$setObjects = array("apple", "grape", "melon", "tomato", "strawberry");
// == END DECLARE VARIABLES ==


// TEST STUFF HERE
// DELETE
while (1) {
  for ($c = 1; $c<=100000; $c++) {
    $location = new Riak\Location($c, $bucket);
    echo("Deleting object at: " . "/buckets/" . $bucket_name .  "/keys/" . $c . "\n");
    deleteData($riak, $location);
  }
}
// END TEST STUFF

?>
