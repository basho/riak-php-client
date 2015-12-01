<?PHP
/*
Project name: Riak Simple PHP Functions Main File
Created by: Allan Paul Sy Ortile
Version: 0.2
Version History:
  - 0.1 20150929 - Created some basic templates for simpler riak PHP use
  - 0.2 20150930 - Created some basic delete functions for bucket and cluster cleanup
      - Forked the riak-php-client so I could drop my files on the example folder
Notes:
  - This still needs to have the Riak PHP client installed, follow: https://github.com/basho/riak-php-client
  - This file only serves as a guide on how to use the Riak client on a functional way (rather than the objectified way)
How to use:
  - Make sure all of the files contains the correct path for the require section
  - Copy and paste from where it indicates -- Start Copy -- to -- End Copy --
  - Copy and paste the functions you need
  - Declare variables as needed
  - Note that in order for you to use bucket data types, you need to create and activiate it
      Refer to: http://docs.basho.com/riak/latest/dev/using/data-types/#Setting-Up-Buckets-to-Use-Riak-Data-Types
Todo:
  - Error handlers, return values and stuff to indicate result of operation
  - Maps in maps (or anything that recurses inside a map)
  - Remove for things inside the map (remove flags, registers, counters, sets, and maps)
  - Convert node initialization to cluster initialization
  - Add guide to what the variables needs to accept
  - BUG: Massive deletions skips some records. UGH. I dont know why
  - Insert JSON objects for basic operations
Disclaimer:
  - This code does not come with any warranty whatsoever. Do not blame the author for any untoward incident
    that happens to you, your family, your dog, or any of your property. Use wisely.
*/

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
$node_name = 'RIAK@127.0.0.1';
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

// END TEST STUFF


/*
 == BUCKET TYPE FUNCTIONS FOR BASIC RIAK OPERATIONS ==
          BasicOperations.php
*/

//echo("Storing object at: " . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//putData($riak, $location, $object);

//echo("Getting object at: " . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//echo getData($riak, $location); // Function returns the value

//echo("Deleting object at: " . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//deleteData($riak, $location);

/*
 == BUCKET TYPE FUNCTIONS FOR COUNTERS ==
         CounterOperations.php
*/

// Put counter
//echo("Storing object at: /types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . " with increment value: " . $counterIncrementValue . "\n" );
//putCounterData($riak, $location, $counterIncrementValue);

// Get counter
//echo("Getting object at: /types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n" );
// RETURNS AN INTEGER
//echo getCounterData($riak, $location);

// Delete counter (reused function from basic operations)
//echo("Deleting object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//deleteData($riak, $location);

/*
 == BUCKET TYPE FUNCTIONS FOR SETS ==
        SetOperations.php
*/

// Put sets
//echo("Storing object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//putSetData($riak, $location, $setObjects);

// Get sets
//echo("Getting object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
// RETURNS AN ARRAY
//print_r(getSetData($riak, $location));

// Delete sets (reused function from basic operations)
//echo("Deleting object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//deleteData($riak, $location);

/*
 == BUCKET TYPE FUNCTIONS FOR MAPS ==
         MapOperations.php
*/

// Put flag in map
//echo("Storing register object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . " with flag name: " . $flagName . " and value: " . $flagValue  . "\n");
//putMapDataFlag($riak, $location, $flagName, $flagValue);

// Get flag in map
//echo("Getting register object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . " with flag name: " . $flagName . "\n");
// RETURNS 1 for TRUE and BLANK/NULL for FALSE
//echo getMapDataFlag($riak, $location, $flagName);

// Put register in map
//echo("Storing register object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//putMapDataRegister($riak, $location, $registerName, $registerValue);

// Get register in map
//echo("Getting register object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
// RETURNS A STRING
//echo getMapDataRegister($riak, $location, $registerName);

// Put counter in map
//echo("Storing counter object at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . " with increment value: " . $counterIncrementValue . "\n" );
//putMapDataCounter($riak, $location, $counterName, $counterIncrementValue);

// Get counter in map
//echo("Getting counter at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
// RETURNS AN INTEGER
//echo getMapDataCounter($riak, $location, $counterName);

// Put set in map
//echo("Storing set objects at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
//putMapDataSets($riak, $location, $setName, $setObjects);

// Get set in map
//echo("Getting set objects at: types/" . $bucket_type . "/buckets/" . $bucket_name .  "/keys/" . $key_name . "\n");
// RETURNS AN ARRAY
//print_r (getMapDataSets($riak, $location, $setName));

/*
 == SOME DEBUG/CLEANUP RELATED OPERATIONS ==
*/

// Vardump for checking values
//echo getMapVarDump($riak, $location);

// WARNING: BUCKET WIDE DESTRUCTIVE FUNCTION
// IM PROVIDING A VARIABLE OVERRIDE SO AS NOT TO ACCIDENTALLY DELETE THE CONFIGURED ABOVE
//$bucket_name = "my_bucket";
//$bucket_type = "";
//deleteTargetBucket($riak, $bucket_name, $bucket_type);

// WARNING: CLUSTER WIDE DESTRUCTIVE FUNCTION!!!
// THIS WILL DELETE ALL KEYS IN ALL BUCKETS IN YOUR CLUSTER (From Riak node above)
// AVOID SHOOTING YOURSELF AT THE FOOT
//deleteAllBucketContents($riak, $node_name, $node_port, $bucket_type);

// For posterity (and your sanity)
echo "\n";

?>
