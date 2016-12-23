<?php

namespace Pim\Bundle\CustomEntityBundle\Processor\Denormalization;

use Akeneo\Component\Batch\Item\FileInvalidItem;
use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Component\StorageUtils\Detacher\ObjectDetacherInterface;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Component\Connector\Exception\InvalidItemFromViolationsException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class Processor implements ItemProcessorInterface, StepExecutionAwareInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var Registry */
    protected $confRegistry;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var ObjectDetacherInterface */
    protected $detacher;

    /** @var StepExecution */
    protected $stepExecution;

    /**
     * @param EntityManagerInterface $em
     * @param Registry $confRegistry
     * @param ValidatorInterface $validator
     * @param ObjectDetacherInterface $detacher
     */
    public function __construct(
        EntityManagerInterface $em,
        Registry $confRegistry,
        ValidatorInterface $validator,
        ObjectDetacherInterface $detacher
    ) {
        $this->em           = $em;
        $this->confRegistry = $confRegistry;
        $this->validator    = $validator;
        $this->detacher     = $detacher;
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        $entity = $this->findOrCreateObject($item);
        foreach ($item as $key => $value) {
            // TODO: Move in an updater
            $method = 'set'. Inflector::classify($key);
            $entity->$method($value);
        }

        $violations = $this->validator->validate($entity);
        if ($violations->count() > 0) {
            $this->objectDetacher->detach($entity);
            $this->skipItemWithConstraintViolations($item, $violations);
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    protected function findOrCreateObject(array $item)
    {
        $entity = $this->findObject($item);
        if (null === $entity) {
            $className = $this->getClassName();
            $entity = new $className();
        }

        return $entity;
    }

    protected function findObject(array $item)
    {
        if (!isset($item['code'])) {
            throw new \Exception('Code not found on the line');
        }

        return $this->getRepository()->findOneByIdentifier($item['code']);
    }

    /**
     * @return CustomEntityRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository($this->getClassName());
    }

    protected function getClassName()
    {
        $customEntityName = 'brand'; // TODO: Inject in batch job

        return $this->confRegistry->get($customEntityName)->getEntityClass();
    }

    /**
     * @param array $itemskip
     * @param ConstraintViolationListInterface $violations
     *
     * @throws InvalidItemFromViolationsException
     */
    protected function skipItemWithConstraintViolations(
        array $item,
        ConstraintViolationListInterface $violations
    ) {
        if ($this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('skip');
        }

        throw new InvalidItemFromViolationsException(
            $violations,
            new FileInvalidItem($item, ($this->stepExecution->getSummaryInfo('read_lines') + 1)),
            [],
            0
        );
    }
}
