<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Reader\Database;

use Akeneo\Component\Batch\Model\StepExecution;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Component\Connector\Reader\Database\AbstractReader;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class ReferenceDataReader extends AbstractReader
{
    /** @var Registry */
    protected $confRegistry;

    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param Registry $confRegistry
     * @param EntityManagerInterface $em
     */
    public function __construct(Registry $confRegistry, EntityManagerInterface $em)
    {
        $this->confRegistry = $confRegistry;
        $this->em           = $em;
    }

    /**
     * {@inheritdoc}
     */
    protected function getResults()
    {
        $referenceDataName  = $this->stepExecution->getJobParameters()->get('reference_data_name');
        $referenceDataClass = $this->confRegistry->get($referenceDataName)->getEntityClass();

        return new \ArrayIterator(
            $this->em->getRepository($referenceDataClass)->findAll()
        );
    }
}
