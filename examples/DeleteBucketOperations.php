<?PHP
/*
Project name: Riak Simple PHP Functions Basic Operations
Created by: Allan Paul Sy Ortile

WARNING: This is a destructive function. USE WITH CAUTION!

*/

// This requires the absolute path to the autoload.php file from the riak-php-client installation
require __DIR__ . '/../vendor/autoload.php';

// This includes a couple of commonly used libraries
use Basho\Riak;
use Basho\Riak\Location;
use Basho\Riak\Node;
use Basho\Riak\Command;
use Basho\Riak\Bucket;

// Loops through all of the keys in the bucket
//getBuckets($node_name, $node_port, $bucket_type);
function getBuckets($node_name, $node_port, $bucket_type) {
  // Get the values after the @ sign to get the IP of the node
  $node_ip = substr($node_name, strrpos($node_name, '@') + 1);
  $curl = curl_init();
  // Check if using a custom bucket type
  if (empty($bucket_type)) {
    // Use default bucket type
    //echo "Curling from: " . 'http://'.$node_ip.':'.$node_port.'/buckets?buckets=true';
    curl_setopt($curl, CURLOPT_URL, 'http://'.$node_ip.':'.$node_port.'/buckets?buckets=true');
  } else {
    // Use custom bucket type
    //echo "Curling from: " . 'http://'.$node_ip.':'.$node_port.'/types/'.$bucket_type.'/buckets?buckets=true';
    curl_setopt($curl, CURLOPT_URL, 'http://'.$node_ip.':'.$node_port.'/types/'.$bucket_type.'/buckets?buckets=true');
  }
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $json = curl_exec ($curl);
  curl_close ($curl);
  // should output a list of buckets to traverse
  $arrBuckets = (json_decode($json, true));

  /*
  foreach ($arrBuckets['buckets'] as $item) {
    echo $item . "\n";
  }
  */

  return $arrBuckets;
}

// takes in a bucket name as an argument
//getKeys($node_name, $node_port, $bucket_type, $bucket_name);
function getKeys($node_name, $node_port, $bucket_type, $bucket_name) {
  // Get the values after the @ sign to get the IP of the node
  $node_ip = substr($node_name, strrpos($node_name, '@') + 1);
  $curl = curl_init();
  // Check if using a custom bucket type
  if (empty($bucket_type)) {
    // Use default bucket type
    //echo "Curling from: " . 'http://'.$node_ip.':'.$node_port.'/buckets/'.$bucket_name.'/keys?keys=true';
    curl_setopt($curl, CURLOPT_URL, 'http://'.$node_ip.':'.$node_port.'/buckets/'.$bucket_name.'/keys?keys=true');
  } else {
    // Use custom bucket type
    //echo "Curling from: " . 'http://'.$node_ip.':'.$node_port.'/types/'.$bucket_type.'/buckets/'.$bucket_name.'/keys?keys=true';
    curl_setopt($curl, CURLOPT_URL, 'http://'.$node_ip.':'.$node_port.'/types/'.$bucket_type.'/buckets/'.$bucket_name.'/keys?keys=true');
  }
  //'http://localhost:10018/buckets/'.$bucket.'/keys?keys=true'
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $json = curl_exec ($curl);
  curl_close ($curl);
  // should output a list of buckets to traverse
  $arrKeys = (json_decode($json, true));
  return $arrKeys;
}

//deleteAllBucketContents($riak, $node_name, $node_port, $bucket_type);
function deleteAllBucketContents($riak, $node_name, $node_port, $bucket_type) {
  // Fetch the bucket list as an array, the node_name and the node_port is to build the URL
  $arrBuckets = getBuckets($node_name, $node_port, $bucket_type);
  // Loop through all of the buckets
  foreach ($arrBuckets['buckets'] as $bucket_name) {
      // Output the current bucket
      echo "CURRENT BUCKET: " . $bucket_name . "\n";
      // Get all of the keys in the current bucket
      $arrKeys = getKeys($node_name, $node_port, $bucket_type, $bucket_name);
      // Prep the bucket to be used by Riak
      if (empty($bucket_type)) {
        // For default bucket types
        $bucket = new Riak\Bucket($bucket_name);
      } else {
        $bucket = new Riak\Bucket($bucket_name, $bucket_type);
      }
      // Loop through all the keys in the current bucket
      //print_r($arrKeys);
      foreach ($arrKeys['keys'] as $key) {
          // Prep the keys to be used by Riak location
          $location = new Riak\Location($key, $bucket);
          echo "Deleting key: " . $key . " from bucket: " . $bucket_name . "\n";
          deleteData($riak, $location);
          }
      }

}


function deleteTargetBucket($riak, $bucket_name, $bucket_type) {
  // Check if using a custom bucket type
  if (empty($bucket_type)) {
    // Use default bucket type
    $bucket = new Riak\Bucket($bucket_name);
  } else {
    // Use custom bucket type
    $bucket = new Riak\Bucket($bucket_name, $bucket_type);
  }

  // Output the current bucket
  echo "CURRENT BUCKET: " . $bucket_name . "\n";
  // Get all of the keys in the current bucket
  $arrKeys = getKeys($node_name, $node_port, $bucket_type, $bucket_name);
  // Prep the bucket to be used by Riak
  $bucket = new Riak\Bucket($bucket_name);
  // Loop through all the keys in the current bucket
  //print_r($arrKeys);
  foreach ($arrKeys['keys'] as $key) {
      // Prep the keys to be used by Riak location
      $location = new Riak\Location($key, $bucket);
      echo "Deleting key: " . $key . " from bucket: " . $bucket_name . "\n";
      deleteData($riak, $location);
      }

}

?>
