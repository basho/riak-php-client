<?php

namespace Basho\Riak\Annotation;

/**
 * Annotates a field in a class to serve as the vector clock.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class VClock implements RiakAnnotation
{

}
