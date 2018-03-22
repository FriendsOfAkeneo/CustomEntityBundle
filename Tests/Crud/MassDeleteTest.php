<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Acme\Bundle\CustomBundle\Entity\Color;

class MassDeleteTest extends AbstractCrudTestCase
{
    public function testMassDeleteReferenceData()
    {
        $color1 = $this->createColor1();
        $color2 = $this->createColor2();

        $colors = $this->manager->findAll(Color::class);
        $this->assertCount(2, $colors);

        $this->em->getRepository(Color::class)->deleteFromIds([
            $color1->getId(),
            $color2->getId(),
        ]);

        $this->em->clear();

        $colors = $this->manager->findAll(Color::class);
        $this->assertCount(0, $colors);
    }

    private function createColor1()
    {
        $data = [
            'code' => 'blue',
            'name' => 'Blue',
            'hex' => '#0007FF',
            'red' => 0,
            'green' => 7,
            'blue' => 255,
        ];

        return $this->createReferenceData(Color::class, $data);
    }

    private function createColor2()
    {
        $data = [
            'code' => 'red',
            'name' => 'Red',
            'hex' => '#FF0000',
            'red' => 255,
            'green' => 0,
            'blue' => 0,
        ];

        return $this->createReferenceData(Color::class, $data);
    }
}
