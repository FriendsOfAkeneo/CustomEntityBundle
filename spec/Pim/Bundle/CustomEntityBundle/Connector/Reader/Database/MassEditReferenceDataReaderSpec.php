<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Reader\Database;

use Akeneo\Component\Batch\Item\ItemReaderInterface;
use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Model\JobExecution;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Doctrine\ORM\EntityManager;
use Pim\Bundle\CustomEntityBundle\Connector\Reader\Database\MassEditReferenceDataReader;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Component\ReferenceData\ConfigurationRegistry;

class MassEditReferenceDataReaderSpec extends ObjectBehavior
{
    function let(EntityManager $em, ConfigurationRegistry $registry, StepExecution $stepExecution)
    {
        $this->beConstructedWith($em, $registry);
        $this->setStepExecution($stepExecution);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MassEditReferenceDataReader::class);
    }

    function it_is_an_item_reader()
    {
        $this->shouldImplement(ItemReaderInterface::class);
    }

    function it_is_step_execution_aware()
    {
        $this->shouldImplement(StepExecutionAwareInterface::class);
    }

    function it_reads_reference_data_from_database(
        $em,
        $stepExecution,
        JobExecution $jobExecution,
        JobParameters $jobParameters,
        CustomEntityRepository $repository
    ) {
        $someCustomentity = function($id = null) {
            return new class($id) {
                private $id;
                public function __construct($id) {
                    $this->id = $id;
                }
                public function getId() {
                    return $this->id;
                }
            };
        };

        $jobParameters->get('reference_data')->willReturn('some_custom_entity');
        $jobParameters->get('ids')->willReturn([44, 56]);
        $jobExecution->getJobParameters()->willReturn($jobParameters);
        $stepExecution->getJobExecution()->willReturn($jobExecution);

        $customEntities = [$someCustomentity(44), $someCustomentity(56)];
        $repository->findByIds([44, 56])->willReturn(new \ArrayIterator($customEntities));
        $em->getRepository('some_custom_entity')->willReturn($repository);

        $stepExecution->incrementSummaryInfo('read')->shouldBeCalledTimes(2);

        $item = $this->read();
        $item->shouldHaveType(get_class($someCustomentity()));
        $item->getId()->shouldReturn(44);

        $item = $this->read();
        $item->shouldHaveType(get_class($someCustomentity()));
        $item->getId()->shouldReturn(56);

        $this->read()->shouldReturn(null);
    }
}
