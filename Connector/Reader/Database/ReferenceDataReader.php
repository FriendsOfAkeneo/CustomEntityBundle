<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Reader\Database;

use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Component\Connector\Reader\Database\AbstractReader;

/**
 * Reference data database reader
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
