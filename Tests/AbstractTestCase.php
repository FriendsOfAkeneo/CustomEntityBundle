<?php

namespace Pim\Bundle\CustomEntityBundle\Tests;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Akeneo\Test\IntegrationTestsBundle\Configuration\CatalogInterface;
use Akeneo\Test\IntegrationTestsBundle\Security\SystemUserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractTestCase extends KernelTestCase
{
    /** @var KernelInterface */
    protected $testKernel;

    /** @var CatalogInterface */
    protected $catalog;

    /** @var EntityManagerInterface */
    protected $em;

    public function setUp()
    {
        static::bootKernel(['debug' => false]);

        $container = static::$kernel->getContainer();
        $authenticator = new SystemUserAuthenticator($container);
        $authenticator->createSystemUser();

        $kernelClass = class_exists(
            'PimEnterprise\Bundle\WorkflowBundle\PimEnterpriseWorkflowBundle'
        ) ? AppKernelTestEe::class : AppKernelTest::class;

        $this->testKernel = new $kernelClass('test', false);
        $this->testKernel->boot();

        $this->catalog = $this->testKernel->getContainer()->get('akeneo_integration_tests.configuration.catalog');
        $this->testKernel->getContainer()->set(
            'akeneo_integration_tests.catalog.configuration',
            $this->catalog->useMinimalCatalog()
        );

        $fixturesLoader = $this->testKernel->getContainer()->get('akeneo_integration_tests.loader.fixtures_loader');
        $fixturesLoader->load();

        $this->em = $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param $serviceName
     *
     * @return object
     */
    protected function get($serviceName)
    {
        return $this->testKernel->getContainer()->get($serviceName);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    protected function getParameter($name)
    {
        return $this->testKernel->getContainer()->getParameter($name);
    }

    /**
     * @param array $input
     *
     * @return int
     */
    protected function runBatchCommand(array $input = [])
    {
        $application = new Application($this->testKernel);
        $batchCommand = new BatchCommand();
        $batchCommand->setContainer($this->testKernel->getContainer());
        $application->add($batchCommand);

        if (!array_key_exists('--no-log', $input)) {
            $input['--no-log'] = true;
        }

        $batch = $application->find('akeneo:batch:job');

        return $batch->run(new ArrayInput($input), new NullOutput());
    }
}
