<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Processor\Denormalization;

use Akeneo\Component\Batch\Item\FileInvalidItem;
use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Component\StorageUtils\Detacher\ObjectDetacherInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\LocaleRepository;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Component\Connector\Exception\InvalidItemFromViolationsException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Generic denormalizer processor for reference data
 * Only works for basic reference data
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceDataProcessor implements ItemProcessorInterface, StepExecutionAwareInterface
{
    /** @var Registry */
    protected $confRegistry;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ObjectUpdaterInterface */
    protected $updater;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var ObjectDetacherInterface */
    protected $detacher;

    /** @var StepExecution */
    protected $stepExecution;

    /** @var LocaleRepository */
    protected $localeRepository;

    /**
     * ReferenceDataProcessor constructor.
     * @param Registry $confRegistry
     * @param EntityManagerInterface $em
     * @param ObjectUpdaterInterface $updater
     * @param ValidatorInterface $validator
     * @param ObjectDetacherInterface $detacher
     * @param LocaleRepository $localeRepository
     */
    public function __construct(
        Registry $confRegistry,
        EntityManagerInterface $em,
        ObjectUpdaterInterface $updater,
        ValidatorInterface $validator,
        ObjectDetacherInterface $detacher,
        LocaleRepository $localeRepository
    ) {
        $this->confRegistry     = $confRegistry;
        $this->em               = $em;
        $this->updater          = $updater;
        $this->validator        = $validator;
        $this->detacher         = $detacher;
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        if (!isset($item['code'])) {
            throw new \RuntimeException(sprintf('Column "%s" is mandatory', 'code'));
        }

        $entity = $this->findOrCreateObject($item);

        if ($entity instanceof \Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity) {
            $item = $this->denormalizeTranslations($item);
        }

        try {
            $this->updater->update($entity, $item);
        } catch (\Exception $e) {
            $this->skipItemWithMessage($item, $e->getMessage(), $e);
        }

        $violations = $this->validator->validate($entity);
        if ($violations->count() > 0) {
            $this->detacher->detach($entity);
            $this->skipItemWithConstraintViolations($item, $violations);
        }

        return $entity;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function denormalizeTranslations(array $item):array
    {
        $activatedLocales = $this->localeRepository->getActivatedLocaleCodes();

        foreach ($item as $key => $value) {
            foreach ($activatedLocales as $localeCode) {
                if (preg_match('/.*-(' . preg_quote($localeCode) . ')$/', $key, $match) === 1) {
                    $attributeCode = str_replace('-' . $localeCode, '', $key);

                    $item[$attributeCode] = $item[$attributeCode] ?? [];

                    if (!empty($value)) {
                        $item[$attributeCode][$localeCode] = $value;
                    }

                    unset($item[$key]);
                }
            }
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    /**
     * Finds or create reference data entity
     *
     * @param array $item
     *
     * @return null|object
     */
    protected function findOrCreateObject(array $item)
    {
        $entity = $this->findObject($item);
        if (null === $entity) {
            $className = $this->getClassName();
            $entity = new $className();
        }

        return $entity;
    }

    /**
     * Finds reference data entity
     *
     * @param array $item
     *
     * @return null|object
     */
    protected function findObject(array $item)
    {
        return $this->getRepository()->findOneByIdentifier($item['code']);
    }

    /**
     * @return CustomEntityRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository($this->getClassName());
    }

    /**
     * Gets class name from the conf registry
     *
     * @return string
     */
    protected function getClassName()
    {
        $referenceDataName = $this->stepExecution->getJobParameters()->get('reference_data_name');

        return $this->confRegistry->get($referenceDataName)->getEntityClass();
    }

    /**
     * @param array $item
     * @param ConstraintViolationListInterface $violations
     *
     * @throws InvalidItemFromViolationsException
     */
    protected function skipItemWithConstraintViolations(
        array $item,
        ConstraintViolationListInterface $violations
    ) {
        $this->stepExecution->incrementSummaryInfo('skip');

        throw new InvalidItemFromViolationsException(
            $violations,
            new FileInvalidItem($item, ($this->stepExecution->getSummaryInfo('read_lines') + 1))
        );
    }

    /**
     * Sets an item as skipped and throws an invalid item exception
     *
     * @param array      $item
     * @param \Exception $previousException
     * @param string     $message
     *
     * @throws InvalidItemException
     */
    protected function skipItemWithMessage(array $item, $message, \Exception $previousException = null)
    {
        if ($this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('skip');
        }

        $invalidItem = new FileInvalidItem(
            $item,
            ($this->stepExecution->getSummaryInfo('read_lines') + 1)
        );

        throw new InvalidItemException($message, $invalidItem, [], 0, $previousException);
    }
}
