<?php

namespace Pim\Bundle\CustomEntityBundle\Metadata;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class Metadata registry to get readable and writable properties from a class
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class ClassMetadataRegistry
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var array */
    protected $classes = [];

    /** @var array */
    protected $readableProperties = [];

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param string $className
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass(string $className)
    {
        if (!isset($this->classes[$className])) {
            $this->classes[$className] = new \ReflectionClass($className);
        }

        return $this->classes[$className];
    }

    /**
     * @param object $object
     *
     * @return string[]
     */
    public function getReadableProperties($object)
    {
        $className = ClassUtils::getClass($object);

        $reflectionClass = $this->getReflectionClass($className);
        if (!isset($this->readableProperties[$className])) {
            $properties = $reflectionClass->getProperties();

            $readableProperties = [];
            foreach ($properties as $property) {
                if ($this->propertyAccessor->isReadable($object, $property->getName())) {
                    $readableProperties[] = $property->getName();
                }
            }

            $this->readableProperties[$className] = $readableProperties;
        }

        return $this->readableProperties[$className];
    }
}
