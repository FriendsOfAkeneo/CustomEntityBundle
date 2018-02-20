<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Tests\AbstractTestCase;
use Pim\Component\Catalog\Model\ChannelInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractCrudTestCase extends AbstractTestCase
{
    /** @var ManagerInterface */
    protected $manager;

    public function setUp()
    {
        parent::setUp();
        $this->manager = $this->get('pim_custom_entity.manager');
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

    protected function loadData()
    {
        $this->createReferenceData(Color::class, [
            'code' => 'my_blue',
            'name' => 'My blue',
            'hex' => '#0007FF',
            'red' => 0,
            'green' => 7,
            'blue' => 255,
        ]);

        $this->createReferenceData(Pictogram::class, [
            'code' => 'my_picto',
        ]);

        $this->createReferenceData(Fabric::class, [
            'code' => 'my_fabric',
            'name' => 'My fabric',
        ]);

        $this->createReferenceData(Brand::class, [
            'code' => 'my_brand',
        ]);
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
