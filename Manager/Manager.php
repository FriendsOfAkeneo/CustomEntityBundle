<?php

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Akeneo\Bundle\StorageUtilsBundle\Doctrine\SmartManagerRegistry;
use Akeneo\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Base implementation for ORM managers
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Manager implements ManagerInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var SaverInterface */
    protected $saver;

    /** @var RemoverInterface */
    protected $remover;

    /**
     * @param EntityManagerInterface    $em
     * @param PropertyAccessorInterface $propertyAccessor
     * @param SaverInterface            $saver
     * @param RemoverInterface          $remover
     */
    public function __construct(
        EntityManagerInterface $em,
        PropertyAccessorInterface $propertyAccessor,
        SaverInterface $saver,
        RemoverInterface $remover
    ) {
        $this->em      = $em;
        $this->propertyAccessor = $propertyAccessor;
        $this->saver    = $saver;
        $this->remover  = $remover;
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
        return $this->em->getRepository($entityClass)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, array $options = array())
    {
        $this->saver->save($entity, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity)
    {
        $this->remover->remove($entity);
    }
}
