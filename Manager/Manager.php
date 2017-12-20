<?php

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Akeneo\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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

    /** @var ObjectUpdaterInterface */
    protected $updater;

    /** @var SaverInterface */
    protected $saver;

    /** @var RemoverInterface */
    protected $remover;

    /** @var NormalizerInterface */
    private $normalizer;

    /**
     * @param EntityManagerInterface $em
     * @param ObjectUpdaterInterface $updater
     * @param SaverInterface         $saver
     * @param RemoverInterface       $remover
     * @param NormalizerInterface    $normalizer
     */
    public function __construct(
        EntityManagerInterface $em,
        ObjectUpdaterInterface $updater,
        SaverInterface $saver,
        RemoverInterface $remover,
        NormalizerInterface $normalizer
    ) {
        $this->em = $em;
        $this->updater = $updater;
        $this->saver = $saver;
        $this->remover = $remover;
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entityClass, array $defaultValues = [], array $options = [])
    {
        $referenceData = new $entityClass();
        $this->updater->update($referenceData, $defaultValues);

        return $referenceData;
    }

    /**
     * {@inheritdoc}
     */
    public function find($entityClass, $id, array $options = [])
    {
        return $this->em->getRepository($entityClass)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($entityClass) : array
    {
        return $this->em->getRepository($entityClass)->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity, array $normalizedData): void
    {
        $this->updater->update($entity, $normalizedData);
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, ?array $options = []): void
    {
        $this->saver->save($entity, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity): void
    {
        $this->remover->remove($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        return $this->normalizer->normalize($entity, $format, $context);
    }
}
