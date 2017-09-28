<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Akeneo\Bundle\BatchBundle\Job\JobInstanceRepository;
use Akeneo\Component\Batch\Model\JobInstance;
use Akeneo\Bundle\BatchBundle\Launcher\JobLauncherInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Action\QuickExportAction;
use Pim\Bundle\CustomEntityBundle\Configuration\Configuration;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Pim\Bundle\DataGridBundle\Extension\MassAction\MassActionDispatcher;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

class QuickExportActionSpec extends ObjectBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        MassActionDispatcher $massActionDispatcher,
        JobInstanceRepository $jobInstanceRepository,
        JobLauncherInterface $jobLauncher,
        TokenStorageInterface $tokenStorage,
        Request $request,
        ParameterBag $attributes,
        ConfigurationInterface $configuration,
        Session $session,
        FlashBag $flashBag
    ) {
        $this->beConstructedWith(
            $actionFactory,
            $eventManager,
            $managerRegistry,
            $router,
            $translator,
            $massActionDispatcher,
            $jobInstanceRepository,
            $jobLauncher,
            $tokenStorage
        );

        // initialize configuration
        $configuration->getEntityClass()->willReturn('entity_class');
        $configuration->getName()->willReturn('entity');

        // initialize manager
        $managerRegistry->getFromConfiguration($configuration)->willReturn($manager);

        // initialize router
        $router->generate(Argument::type('string'), Argument::type('array'), Argument::any())->will(
            function ($arguments) {
                $path = $arguments[0] . '?';
                foreach ($arguments[1] as $key => $value) {
                    $path .= '&' . $key . '=' . $value;
                }

                return $path;
            }
        );

        // initialize request
        $request->attributes = $attributes;
        $attributes->get('id')->willReturn('id');

        // initialize flashbag
        $request->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

        // initialize translator
        $translator->trans(Argument::type('string'), Argument::any())->will(
            function ($arguments) {
                if (!isset($arguments[1])) {
                    $arguments[1] = array();
                }

                $translated = sprintf('<%s>', $arguments[0]);
                foreach ($arguments[1] as $key => $value) {
                    $translated .= sprintf('%s=%s;', $key, $value);
                }

                return $translated;
            }
        );
    }

    public function it_is_an_action_interface()
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(QuickExportAction::class);
    }

    public function it_returns_quick_export_as_type()
    {
        $this->getType()->shouldReturn('quick_export');
    }

    public function it_launches_a_job_in_backend(
        $jobInstanceRepository,
        $jobLauncher,
        $tokenStorage,
        $massActionDispatcher,
        $request,
        $configuration,
        JobInstance $jobInstance,
        TokenInterface $token,
        UserInterface $user
    ) {
        $jobInstanceRepository->findOneBy(['code' => 'csv_reference_data_quick_export'])->willReturn($jobInstance);

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $massActionDispatcher->dispatch($request)->willReturn([2, 5]);

        $configuration
            ->getActionOptions('quick_export')
            ->willReturn(['job_profile' => 'csv_reference_data_quick_export']);

        $rawConfig = [
            'reference_data' => 'entity_class',
            'ids'            => [2, 5]
        ];

        $jobLauncher->launch($jobInstance, $user, $rawConfig)->shouldBeCalled();

        $this->setConfiguration($configuration);
        $this->doExecute($request);
    }

    public function it_throws_an_exception_when_job_instance_does_not_exist(
        $jobInstanceRepository,
        $request,
        Configuration $configuration
    ) {
        $configuration
            ->getActionOptions('quick_export')
            ->willReturn(['job_profile' => 'csv_reference_data_quick_export']);

        $jobInstanceRepository->findOneBy(['code' => 'csv_reference_data_quick_export'])->willReturn(null);

        $this->setConfiguration($configuration);
        $this->shouldThrow(
            new \LogicException(
                'The job instance "csv_reference_data_quick_export" does not exist. Please contact your administrator'
            )
        )
        ->duringDoExecute($request);
    }

    public function it_throws_an_exception_when_user_is_no_longer_authenticated(
        $jobInstanceRepository,
        $request,
        $tokenStorage,
        $configuration,
        $massActionDispatcher,
        JobInstance $jobInstance
    ) {
        $jobInstanceRepository->findOneBy(['code' => 'csv_reference_data_quick_export'])->willReturn($jobInstance);

        $massActionDispatcher->dispatch($request)->willReturn([]);
        $tokenStorage->getToken()->willReturn(null);

        $configuration->getActionOptions('quick_export')->willReturn([]);

        $this->setConfiguration($configuration);
        $this
            ->shouldThrow(
                new TokenNotFoundException('You are no longer authenticated. Please log in and try again')
            )
            ->duringDoExecute($request);
    }

    protected function initializeEventManager(ActionEventManager $eventManager)
    {
        $eventManager
            ->dipatchConfigureEvent(
                $this,
                Argument::type('Symfony\Component\OptionsResolver\OptionsResolver')
            )
            ->shouldBeCalled();

        $eventManager->dispatchPreExecuteEvent($this)->shouldBeCalled();

        $eventManager
            ->dispatchPostExecuteEvent($this, Argument::type('Symfony\Component\HttpFoundation\Response'))
            ->will(
                function ($args) {
                    return $args[1];
                }
            )
            ->shouldBeCalled();

        $eventManager
            ->dispatchPreRenderEvent($this, Argument::type('string'), Argument::type('array'))
            ->will(
                function ($args) {
                    $args[2]['pre_render'] = true;

                    return [$args[1], $args[2]];
                }
            );
    }
}
