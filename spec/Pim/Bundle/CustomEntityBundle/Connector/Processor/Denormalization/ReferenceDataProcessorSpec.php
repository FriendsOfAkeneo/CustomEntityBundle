<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Processor\Denormalization;

use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Component\StorageUtils\Detacher\ObjectDetacherInterface;
use Akeneo\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Connector\Processor\Denormalization\ReferenceDataProcessor;
use Pim\Component\Connector\Exception\InvalidItemFromViolationsException;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Prophecy\Argument;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReferenceDataProcessorSpec extends ObjectBehavior
{
    function let(
        Registry $registry,
        EntityManagerInterface $em,
        ObjectUpdaterInterface $updater,
        ValidatorInterface $validator,
        ObjectDetacherInterface $detacher,
        StepExecution $stepExecution,
        JobParameters $jobParameters,
        IdentifiableObjectRepositoryInterface $objectRepository,
        ConfigurationInterface $config
    ) {
        $jobParameters->get('reference_data_name')->willReturn('my_custom_entity_name');
        $stepExecution->getJobParameters()->willReturn($jobParameters);

        $config->getEntityClass()->willReturn('\stdClass');
        $registry->get('my_custom_entity_name')->willReturn($config);
        $em->getRepository('\stdClass')->willReturn($objectRepository);

        $this->beConstructedWith($registry, $em, $updater, $validator, $detacher);
        $this->setStepExecution($stepExecution);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceDataProcessor::class);
    }

    function it_is_a_processor()
    {
        $this->shouldImplement(ItemProcessorInterface::class);
    }

    function it_is_step_execution_aware()
    {
        $this->shouldImplement(StepExecutionAwareInterface::class);
    }

    function it_throws_exception_when_item_has_no_code()
    {
        $this->shouldThrow(new \RuntimeException(sprintf('Column "%s" is mandatory', 'code')))
             ->during('process', [[]]);
    }

    function it_creates_an_entity_if_it_does_not_exist(
        $objectRepository,
        $updater,
        $validator,
        ConstraintViolationListInterface $violations
    ) {
        $item = [
            'code' => 'foo',
            'bar'  => 'baz',
        ];

        $objectRepository->findOneByIdentifier('foo')->willReturn(null);
        $updater->update(Argument::type(\stdClass::class), $item)->shouldBeCalled();
        $validator->validate(Argument::type(\stdClass::class))->willReturn($violations);
        $violations->count()->willReturn(0);

        $this->process($item);
    }

    function it_updates_an_existing_entity(
        $objectRepository,
        $updater,
        $validator,
        ConstraintViolationListInterface $violations,
        ReferenceDataInterface $entity
    ) {
        $item = [
            'code' => 'foo',
            'bar'  => 'baz',
        ];

        $objectRepository->findOneByIdentifier('foo')->willReturn($entity);
        $updater->update($entity, $item)->shouldBeCalled();
        $validator->validate($entity)->willReturn($violations);
        $violations->count()->willReturn(0);

        $this->process($item);
    }

    function it_skips_item_on_update_exception(
        $objectRepository,
        $updater,
        $stepExecution,
        ReferenceDataInterface $entity
    ) {
        $item = [
            'code' => 'foo',
            'bar'  => 'baz',
        ];

        $objectRepository->findOneByIdentifier('foo')->willReturn($entity);
        $exception = new \Exception('exception_message');
        $updater->update($entity, $item)->willThrow($exception);

        $stepExecution->incrementSummaryInfo('skip')->shouldBeCalled();
        $stepExecution->getSummaryInfo('item_position')->shouldBeCalled();

        $this->shouldThrow(InvalidItemException::class)->during('process', [$item]);
    }

    function it_skips_item_on_constraint_violation(
        $objectRepository,
        $updater,
        $detacher,
        $validator,
        $stepExecution,
        ConstraintViolationListInterface $violations,
        ReferenceDataInterface $entity
    ) {
        $item = [
            'code' => 'foo',
            'bar'  => 'baz',
        ];

        $objectRepository->findOneByIdentifier('foo')->willReturn($entity);
        $updater->update($entity, $item)->shouldBeCalled();
        $validator->validate($entity)->willReturn($violations);
        $violations->rewind()->shouldBeCalled();
        $violations->valid()->shouldBeCalled();
        $violations->count()->willReturn(2);

        $detacher->detach($entity)->shouldBeCalled();
        $stepExecution->incrementSummaryInfo('skip')->shouldBeCalled();
        $stepExecution->getSummaryInfo('item_position')->shouldBeCalled();

        $this->shouldThrow(InvalidItemFromViolationsException::class)->during('process', [$item]);
    }
}
