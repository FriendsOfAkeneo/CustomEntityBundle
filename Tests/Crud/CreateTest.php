<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Tests\AbstractTestCase;
use Pim\Component\Catalog\Repository\ChannelRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CreateTest extends AbstractCrudTestCase
{
    public function testCreateSimpleReferenceData()
    {
        $entityName = Color::class;
        $data = [
            'code' => 'blue',
            'name' => 'Blue',
            'hex' => '#0007FF',
            'red' => 0,
            'green' => 7,
            'blue' => 255,
        ];

        $color = $this->createReferenceData($entityName, $data);

        $colors = $this->manager->findAll($entityName);
        $this->assertCount(1, $colors);

        $dbColor = $colors[0];
        $this->assertEquals($data['code'], $dbColor->getCode());
        $this->assertEquals($data['name'], $dbColor->getName());
        $this->assertEquals($data['hex'], $dbColor->getHex());
        $this->assertEquals($data['red'], $dbColor->getRed());
        $this->assertEquals($data['green'], $dbColor->getGreen());
        $this->assertEquals($data['blue'], $dbColor->getBlue());
    }

    public function testCreateTranslatableReferenceData()
    {
        $entityName = Pictogram::class;
        $data = [
            'code' => 'my_picto',
            'labels' => [
                'en_US' => 'An english label',
                'fr_FR' => 'Un label français',
                'de_DE' => 'Ein deutsches Label',
            ]
        ];

        $this->activateLocales('fr_FR', 'de_DE');
        $picto = $this->createReferenceData($entityName, $data);
        $this->assertCount(3, $picto->getTranslations());

        $pictos = $this->manager->findAll($entityName);
        $this->assertCount(1, $pictos);

        $dbPicto = $pictos[0];
        $this->assertEquals($data['code'], $dbPicto->getCode());

        foreach ($data['labels'] as $locale => $label) {
            $this->assertEquals($label, $dbPicto->getTranslation($locale)->getLabel());
        }
    }

    public function testCreateLinkedReferenceData()
    {
        $fabric = $this->createReferenceData(Fabric::class, [
            'code' => 'my_fabric',
            'name' => 'My fabric',
            'alternative_name' => 'An alternative name for my fabric',
        ]);

        $entityName = Brand::class;
        $data = [
            'code'   => 'my_brand',
        ];

        $brand = $this->createReferenceData($entityName, $data);

        $brands = $this->manager->findAll($entityName);
        $this->assertCount(1, $brands);
        $dbBrand = $brands[0];

        $this->assertEquals($data['code'], $dbBrand->getCode());
        $this->assertNull($dbBrand->getFabric());
    }
}
