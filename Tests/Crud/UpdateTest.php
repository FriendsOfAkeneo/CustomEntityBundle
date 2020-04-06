<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Pictogram;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UpdateTest extends AbstractCrudTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadData();
    }

    public function testUpdateSimpleReferenceData()
    {
        $entityName = Color::class;

        $myBlue = $this->get('doctrine.orm.entity_manager')->getRepository($entityName)->findOneBy(['code' => 'my_blue']);
        $this->assertInstanceOf($entityName, $myBlue);

        $data = [
            'id' => $myBlue->getId(),
            'code' => 'my_blue',
            'name' => 'another name',
            'hex' => '#010FFE',
            'red' => 1,
            'green' => 15,
            'blue' => 254,
        ];

        $this->get('pim_custom_entity.manager')->update($myBlue, $data);
        $this->get('pim_custom_entity.manager')->save($myBlue);
        $this->get('doctrine.orm.entity_manager')->clear();

        $dbColor = $this->get('pim_custom_entity.manager')->find($entityName, $myBlue->getId());

        $this->assertEquals($data['code'], $dbColor->getCode());
        $this->assertEquals($data['name'], $dbColor->getName());
        $this->assertEquals($data['hex'], $dbColor->getHex());
        $this->assertEquals($data['red'], $dbColor->getRed());
        $this->assertEquals($data['green'], $dbColor->getGreen());
        $this->assertEquals($data['blue'], $dbColor->getBlue());
    }

    public function testUpdateTranslatableReferenceData()
    {
        $entityName = Pictogram::class;
        $myPicto = $this->get('doctrine.orm.entity_manager')->getRepository($entityName)->findOneBy(['code' => 'my_picto']);

        $this->assertInstanceOf($entityName, $myPicto);
        $this->assertCount(0, $myPicto->getTranslations());

        $this->activateLocales('fr_FR', 'de_DE');
        $data = [
            'id' => $myPicto->getId(),
            'labels' => [
                'en_US' => 'An english label',
                'fr_FR' => 'Un label français',
                'de_DE' => 'Ein deutsches Label'
            ],
        ];

        $this->get('pim_custom_entity.manager')->update($myPicto, $data);
        $this->get('pim_custom_entity.manager')->save($myPicto);

        $this->em->clear();

        $dbPicto = $this->manager->find($entityName, $myPicto->getId());
        $this->assertCount(3, $dbPicto->getTranslations());

        foreach ($data['labels'] as $locale => $label) {
            $this->assertEquals($label, $dbPicto->getTranslation($locale)->getLabel());
        }
    }

    public function testUpdateReferenceDataWithInactiveLocale()
    {
        $entityName = Pictogram::class;
        $myPicto = $this->em->getRepository($entityName)->findOneBy(['code' => 'my_picto']);

        $this->assertInstanceOf($entityName, $myPicto);
        $this->assertCount(0, $myPicto->getTranslations());

        $data = [
            'id'     => $myPicto->getId(),
            'labels' => [
                'fr_FR' => 'Un label français',

            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Locale "fr_FR" is not activated');

        $this->manager->update($myPicto, $data);
    }

    public function testUpdateLinkedReferenceData()
    {
        $entityName = Brand::class;
        $myBrand = $this->em->getRepository($entityName)->findOneBy(['code' => 'my_brand']);

        $this->assertInstanceOf($entityName, $myBrand);
        $this->assertInstanceOf(Fabric::class, $myBrand->getFabric());
        $this->assertEquals('my_fabric', $myBrand->getFabric()->getCode());

        $data = [
            'id'     => $myBrand->getId(),
            'fabric' => null,
        ];

        $this->manager->update($myBrand, $data);
        $this->manager->save($myBrand);

        $this->em->clear();

        $dbBrand = $this->manager->find($entityName, $myBrand->getId());
        $this->assertNull($dbBrand->getFabric());

        $data = [
            'id'     => $myBrand->getId(),
            'fabric' => 'my_other_fabric',
        ];

        $this->manager->update($dbBrand, $data);
        $this->manager->save($dbBrand);
        unset($dbBrand);

        $this->em->clear();

        $dbBrand = $this->manager->find($entityName, $myBrand->getId());
        $this->assertInstanceOf(Fabric::class, $dbBrand->getFabric());

        $this->assertEquals('my_other_fabric', $dbBrand->getFabric()->getCode());
        $this->assertEquals('Another fabric', $dbBrand->getFabric()->getName());
    }

    protected function loadData()
    {
        $this->createReferenceData(
            Color::class,
            [
                'code'  => 'my_blue',
                'name'  => 'My blue',
                'hex'   => '#0007FF',
                'red'   => 0,
                'green' => 7,
                'blue'  => 255,
            ]
        );

        $this->createReferenceData(
            Pictogram::class,
            [
                'code' => 'my_picto',
            ]
        );

        $this->createReferenceData(
            Fabric::class,
            [
                'code' => 'my_fabric',
                'name' => 'My fabric',
            ]
        );

        $this->createReferenceData(
            Fabric::class,
            [
                'code' => 'my_other_fabric',
                'name' => 'Another fabric',
            ]
        );

        $this->createReferenceData(
            Brand::class,
            [
                'code'   => 'my_brand',
                'fabric' => 'my_fabric',
            ]
        );
    }
}
