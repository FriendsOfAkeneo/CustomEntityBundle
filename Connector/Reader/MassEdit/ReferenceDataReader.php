<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Reader\MassEdit;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Component\Connector\Repository\JobConfigurationRepositoryInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class ReferenceDataReader extends AbstractConfigurableStepElement implements
    ItemReaderInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;

    /** @var JobConfigurationRepositoryInterface */
    protected $jobConfigRepo;

    /** @var EntityManager */
    protected $em;

    /** @var ArrayCollection */
    protected $referenceDatas;

    /**
     * @param JobConfigurationRepositoryInterface $jobConfigRepo
     * @param EntityManager                       $em
     */
    public function __construct(
        JobConfigurationRepositoryInterface $jobConfigRepo,
        EntityManager $em
    ) {
        $this->jobConfigRepo = $jobConfigRepo;
        $this->em            = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $config = $this->getJobConfiguration();

        // TODO: validate configuration (reference_data + ids)
        if (null === $this->referenceDatas) {
            $this->referenceDatas = $this->getReferenceDatas($config['reference_data'], $config['ids']);
        }

        $result = $this->referenceDatas->current();

        if (!empty($result)) {
            $this->stepExecution->incrementSummaryInfo('read');
            $this->referenceDatas->next();
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Get reference data entities from ids
     *
     * @param string $referenceDataName
     * @param array  $ids
     *
     * @return ArrayCollection
     */
    protected function getReferenceDatas($referenceDataName, array $ids)
    {
        $repository = $this->getRepository($referenceDataName);

        return $repository->findByIds($ids);
    }

    /**
     * @param string $referenceDataName
     *
     * @return CustomEntityRepository
     */
    protected function getRepository($referenceDataName)
    {
        return $this->em->getRepository($referenceDataName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getJobConfiguration()
    {
        $jobExecution = $this->stepExecution->getJobExecution();
        $massEditJobConf = $this->jobConfigRepo->findOneBy(['jobExecution' => $jobExecution]);

        if (null === $massEditJobConf) {
            throw new EntityNotFoundException(sprintf(
                'No JobConfiguration found for jobExecution with id "%s"',
                $jobExecution->getId()
            ));
        }

        return json_decode(stripcslashes($massEditJobConf->getConfiguration()), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFields()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}
