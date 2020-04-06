<?php

namespace Pim\Bundle\CustomEntityBundle\Tests;

use Akeneo\Channel\Component\Model\ChannelInterface;
use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Test\Integration\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = $this->get('doctrine.orm.entity_manager');
        $this->manager = $this->get('pim_custom_entity.manager');
    }

    protected function getConfiguration()
    {
        return $this->catalog->useMinimalCatalog();
    }

    /**
     * @param string $refDataName Reference Data FQCN
     * @param array $data
     *
     * @return object
     */
    protected function createReferenceData($refDataName, $data)
    {
        $refData = $this->manager->create($refDataName, $data);
        $this->assertInstanceOf($refDataName, $refData);

        $this->manager->save($refData);
        $this->em->clear();

        return $refData;
    }

    protected function activateLocales()
    {
        // activate locales fr_FR and de_DE
        /** @var ChannelRepositoryInterface $channelRepo */
        $channelRepo = $this->get('pim_catalog.repository.channel');
        /** @var ChannelInterface $defaultScope */
        $defaultScope = $channelRepo->findOneByIdentifier('ecommerce');
        $locales = $defaultScope->getLocaleCodes();
        foreach (func_get_args() as $localeName) {
            $locales[] = $localeName;
        }

        $this->get('pim_catalog.updater.channel')->update($defaultScope, ['locales' => array_unique($locales)]);
        $this->get('pim_catalog.saver.channel')->save($defaultScope);
    }
}
