<?php

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Pim\Bundle\CatalogBundle\Doctrine\SmartManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Doctrine\Common\Util\ClassUtils;

/**
 * Base implementation for ORM managers
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Manager implements ManagerInterface
{
    /** @var SmartManagerRegistry */
    protected $doctrine;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /**
     * Constructor
     *
     * @param SmartManagerRegistry      $doctrine
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(SmartManagerRegistry $doctrine, PropertyAccessorInterface $propertyAccessor)
    {
        $this->doctrine = $doctrine;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entityClass, array $defaultValues = array(), array $options = array())
    {
        $object = new $entityClass();
        foreach ($defaultValues as $propertyPath => $value) {
            $this->propertyAccessor->setValue($object, $propertyPath, $value);
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function find($entityClass, $id, array $options = array())
    {
        return $this->doctrine->getRepository($entityClass)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, array $options = array())
    {
        $em = $this->getManager($entity);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity)
    {
        $em = $this->getManager($entity);
        $em->remove($entity);
        $em->flush();
    }

    /**
     * Returns the manager for an entity
     *
     * @param object|string $entity
     *
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getManager($entity)
    {
        return $this->doctrine->getManagerForClass(
            is_object($entity) ? ClassUtils::getClass($entity) : $entity
        );
    }
}
