<?php

namespace Pim\Bundle\CustomEntityBundle\MassEditConnector\Reader;

use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\ItemReaderInterface;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Component\Connector\Repository\JobConfigurationRepositoryInterface;
use Pim\Component\ReferenceData\ConfigurationRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    protected $jobConfigRepository;

    /** @var EntityManager */
    protected $em;

    /** @var ArrayCollection */
    protected $referenceDatas;

    /** @var  ConfigurationRegistry */
    protected $registry;

    /**
     * @param JobConfigurationRepositoryInterface $jobConfigRepository
     * @param EntityManager                       $em
     * @param ConfigurationRegistry               $registry
     */
    public function __construct(
        JobConfigurationRepositoryInterface $jobConfigRepository,
        EntityManager $em,
        ConfigurationRegistry $registry
    ) {
        $this->jobConfigRepository = $jobConfigRepository;
        $this->em = $em;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $config = $this->getJobConfiguration();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $config = $resolver->resolve($config);

        if (null === $this->referenceDatas) {
            if ($config['reference_data']) {
                $this->referenceDatas = $this->getReferenceDatas($config['reference_data'], $config['ids']);
            }
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
        $massEditJobConf = $this->jobConfigRepository->findOneBy(['jobExecution' => $jobExecution]);

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

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $allowedValues = array_map(function ($configuration) {
            return $configuration->getClass();
        }, $this->registry->all());

        $resolver->setRequired([
            'reference_data',
            'ids'
        ]);
        $resolver->setAllowedValues('reference_data', $allowedValues);
    }
}
