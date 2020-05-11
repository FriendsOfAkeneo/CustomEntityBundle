<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Acme\Bundle\CustomBundle\Entity\Color;
use Pim\Bundle\CustomEntityBundle\Remover\NonRemovableEntityException;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeleteTest extends AbstractCrudTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->createReferenceData(
            Color::class,
            [
                'code' => 'my_blue',
                'name' => 'My blue',
                'hex' => '#0007FF',
                'red' => 0,
                'green' => 7,
                'blue' => 255,
            ]
        );
    }

    public function testDeleteSimpleReferenceData()
    {
        $entityName = Color::class;
        $colors = $this->manager->findAll($entityName);
        $this->assertCount(1, $colors);

        $blue = $colors[0];
        $id = $blue->getId();

        $this->manager->remove($blue);

        $this->em->clear();

        $dbBlue = $this->manager->find($entityName, $id);
        $this->assertNull($dbBlue);
    }

    public function testDeleteLinkedReferenceDataThrowsException()
    {
        $configurationRegistry = $this->get('pim_reference_data.registry');
        $configurationRegistry->registerRaw(
            [

                'class' => Color::class,
                'type'  => 'simple',

            ],
            'color'
        );

        $this->createRefDataAttribute('custom_color', 'color');
        $this->createProduct(
            'my_sku',
            [
                'custom_color' => 'my_blue',
            ]
        );

        sleep(2); // wait for elasticsearch indexation

        $blue = $this->em->getRepository(Color::class)->findOneBy(['code' => 'my_blue']);
        $this->assertInstanceof(Color::class, $blue);

        $this->expectException(NonRemovableEntityException::class);
        $this->expectExceptionMessage(
            'Reference data cannot be removed. It is linked to 1 product(s) with the attribute "custom_color"'
        );
        $this->manager->remove($blue);
    }
}
