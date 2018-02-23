<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Jobs;

use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Akeneo\Bundle\BatchBundle\Command\BatchCommand;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityExportTest extends AbstractJobTestCase
{
    public function testExportSimpleReferenceData()
    {
        $data = [
            [
                'code'  => 'blue',
                'name'  => 'Blue',
                'hex'   => '#0000FF',
                'red'   => 0,
                'green' => 0,
                'blue'  => 255,
            ],
            [
                'code'  => 'red',
                'name'  => 'Red',
                'hex'   => '#FF0000',
                'red'   => 255,
                'green' => 0,
                'blue'  => 0,
            ],
            [
                'code'  => 'green',
                'name'  => 'Green',
                'hex'   => '#00FF00',
                'red'   => 0,
                'green' => 255,
                'blue'  => 0,
            ],
        ];
        foreach ($data as $color) {
            $this->createReferenceData(Color::class, $color);
        }

        $this->createJobInstance(
            'csv_reference_data_export',
            'export',
            'color',
            static::EXPORT_PATH . 'export_colors.csv'
        );

        $status = $this->launch('csv_reference_data_export');

        $this->assertEquals(BatchCommand::EXIT_SUCCESS_CODE, $status);
        $this->assertFileExists(static::EXPORT_PATH . 'export_colors.csv');
        $this->assertFileEquals(static::DATA_FILE_PATH . 'colors.csv', static::EXPORT_PATH . 'export_colors.csv');
    }
}
