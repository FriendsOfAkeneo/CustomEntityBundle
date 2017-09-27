<?php

namespace Acme\Bundle\CustomBundle\Repository;

use Doctrine\ORM\EntityManager;

/**
 * Choices manager
 * This class is used to retrieve the list all entries for en entity.
 * This list is used in the filter form for custom entities datagrid (i.e: Brand).
 *
 * Please note that this can be used for all custom entities.
 *
 * @author Remy Betus <remy.betus@akeneo.com>
 */
class ChoicesRepository
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $className;

    /**
     * @param EntityManager $entityManager
     * @param string        $className
     */
    public function __construct(EntityManager $entityManager, $className)
    {
        $this->entityManager = $entityManager;
        $this->className = $className;
    }

    /**
     * Get choices from entity
     *
     * @return array
     */
    public function getChoices(): array
    {
        $entityRepository = $this->entityManager->getRepository($this->className);

        $values = $entityRepository->findAll();

        $choices = [];

        foreach ($values as $value) {
            $choices[$value->getId()] = $value->getCode();
        }

        return $choices;
    }
}
