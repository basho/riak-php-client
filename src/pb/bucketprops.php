<?php
namespace Riak\PB;

class BucketProps extends Message
{ 
  protected static $fields = array(
    "1" => "Int",
    "2" => "Bool",
  );
  
  protected static $fieldNames = array(
    "1" => "n_val",
    "2" => "allow_mult",
  );
  
  protected $values = array( 
    "1" => null,
    "2" => null,
  );
}
