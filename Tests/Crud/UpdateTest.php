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
    public function setUp()
    {
        parent::setUp();
        $this->loadData();
    }

    public function testUpdateSimpleReferenceData()
    {
        $entityName = Color::class;

        $myBlue = $this->em->getRepository($entityName)->findOneBy(['code' => 'my_blue']);
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

        $this->manager->update($myBlue, $data);
        $this->manager->save($myBlue);
        $this->em->clear();

        $dbColor = $this->manager->find($entityName, $myBlue->getId());

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
        $myPicto = $this->em->getRepository($entityName)->findOneBy(['code' => 'my_picto']);

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

        $this->manager->update($myPicto, $data);
        $this->manager->save($myPicto);

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
        $this->assertNull($myBrand->getFabric());

        $data = [
            'id'     => $myBrand->getId(),
            'fabric' => 'my_fabric',
        ];

        $this->manager->update($myBrand, $data);
        $this->manager->save($myBrand);

        $this->em->clear();

        $dbBrand = $this->manager->find($entityName, $myBrand->getId());
        $this->assertInstanceOf(Fabric::class, $dbBrand->getFabric());

        $this->assertEquals('my_fabric', $dbBrand->getFabric()->getCode());
        $this->assertEquals('My fabric', $dbBrand->getFabric()->getName());

        $data = [
            'id'     => $dbBrand->getId(),
            'fabric' => null,
        ];

        $this->manager->update($dbBrand, $data);
        $this->manager->save($dbBrand);

        $this->em->clear();

        $dbBrand = $this->manager->find($entityName, $myBrand->getId());
        $this->assertNull($dbBrand->getFabric());
    }
}
