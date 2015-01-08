<?php

namespace Basho\Riak\Core\Converter\Hydrator;

use Basho\Riak\Annotation\RiakAnnotation;
use Doctrine\Common\Annotations\Reader;
use ReflectionProperty;
use ReflectionClass;

/**
 * Domain Metadata reader.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DomainMetadataReader
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $mapping = [];

    /**
     * @param \Doctrine\Common\Annotations\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $className
     *
     * @return array
     */
    public function getRiakPropertiesMapping($className)
    {
        if (isset($this->mapping[$className])) {
            return $this->mapping[$className];
        }

        $reflection = new ReflectionClass($className);
        $properties = $reflection->getProperties();
        $metadata   = [];

        foreach ($properties as $property) {
            $metadata = array_merge($this->getPropertyMapping($property), $metadata);
        }

        return $this->mapping[$className] = $metadata;
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return array
     */
    private function getPropertyMapping(ReflectionProperty $property)
    {
        $metadata = [];

        foreach ($this->reader->getPropertyAnnotations($property) as $value) {

            if ( ! $value instanceof RiakAnnotation) {
                continue;
            }

            $class = get_class($value);
            $name  = $property->getName();
            $key   = lcfirst(substr($class, strrpos($class, '\\') + 1));

            $metadata[$key] = $name;
        }

        return $metadata;
    }
}
