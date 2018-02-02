<?php

namespace Pim\Bundle\CustomEntityBundle\Metadata;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Resolves class metadata target entities from property
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class TargetEntityResolver
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param object $object
     * @param string $property
     *
     * @return string
     *
     * @throws \LogicException
     */
    public function getTargetEntityClass($object, $property)
    {
        $className = ClassUtils::getClass($object);

        $classMetadata = $this->em->getClassMetadata($className);
        if ($classMetadata->hasAssociation($property)) {
            return $classMetadata->getAssociationTargetClass($property);
        }

        throw new \LogicException(
            sprintf('Property "%s" is not linked to a target entity for object of type "%s"', $property, $className)
        );
    }
}
