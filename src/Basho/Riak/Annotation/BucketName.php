<?php

namespace Basho\Riak\Annotation;

/**
 * Annotates a field in a class to serve as the bucket name.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class BucketName implements RiakAnnotation
{

}
