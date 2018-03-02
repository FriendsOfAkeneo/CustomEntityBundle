<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Jobs;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Akeneo\Bundle\BatchBundle\Command\BatchCommand;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityExportTest extends AbstractJobTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->loadData();
    }

    public function testExportSimpleReferenceData()
    {
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

    public function testExportTranslatableReferenceData()
    {
        $this->createJobInstance(
            'csv_reference_data_export',
            'export',
            'pictogram',
            static::EXPORT_PATH . 'export_pictos.csv'
        );

        $status = $this->launch('csv_reference_data_export');

        $this->assertEquals(BatchCommand::EXIT_SUCCESS_CODE, $status);
        $this->assertFileExists(static::EXPORT_PATH . 'export_colors.csv');
        $this->assertFileEquals(static::DATA_FILE_PATH . 'pictos.csv', static::EXPORT_PATH . 'export_pictos.csv');
    }

    public function testExportLinkedReferenceData()
    {
        $this->createJobInstance(
            'csv_reference_data_export',
            'export',
            'brand',
            static::EXPORT_PATH . 'export_brands.csv'
        );

        $status = $this->launch('csv_reference_data_export');

        $this->assertEquals(BatchCommand::EXIT_SUCCESS_CODE, $status);
        $this->assertFileExists(static::EXPORT_PATH . 'export_brands.csv');
        $this->assertFileEquals(static::DATA_FILE_PATH . 'brands.csv', static::EXPORT_PATH . 'export_brands.csv');
    }

    public function testExportCollectionLinkedReferenceData()
    {
        $this->createJobInstance(
            'csv_reference_data_export',
            'export',
            'fabric',
            static::EXPORT_PATH . 'export_fabrics.csv'
        );

        $status = $this->launch('csv_reference_data_export');

        $this->assertEquals(BatchCommand::EXIT_SUCCESS_CODE, $status);
        $this->assertFileExists(static::EXPORT_PATH . 'export_fabrics.csv');
        $this->assertFileEquals(static::DATA_FILE_PATH . 'fabrics.csv', static::EXPORT_PATH . 'export_fabrics.csv');
    }

    private function loadData()
    {
        $this->activateLocales('fr_FR', 'de_DE');
        $colors = [
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
        foreach ($colors as $color) {
            $this->createReferenceData(Color::class, $color);
        }

        $pictos = [
            [
                'code'   => 'picto_1',
                'labels' => [
                    'en_US' => 'label 1 en',
                    'fr_FR' => 'label 1 fr',
                    'de_DE' => 'label 1 de',
                ],
            ],
            [
                'code'   => 'picto_2',
                'labels' => [
                    'en_US' => 'label 2 en',
                    'fr_FR' => 'label 2 fr',
                ],
            ],
        ];
        foreach ($pictos as $picto) {
            $this->createReferenceData(Pictogram::class, $picto);
        }

        $fabrics = [
            [
                'code'             => 'first_fabric',
                'name'             => 'First fabric',
                'alternative_name' => 'Super fabric',
                'colors'           => ['green'],
            ],
            [
                'code'   => 'second_fabric',
                'name'   => 'Another fabric',
                'colors' => ['red', 'blue'],
            ],
        ];
        foreach ($fabrics as $fabric) {
            $this->createReferenceData(Fabric::class, $fabric);
        }

        $brands = [
            [
                'code'   => 'super_brand',
                'fabric' => 'second_fabric',
            ],
            [
                'code' => 'another_brand',
            ],
            [
                'code'   => 'third_brand',
                'fabric' => 'first_fabric',
            ],
        ];
        foreach ($brands as $brand) {
            $this->createReferenceData(Brand::class, $brand);
        }
    }
}
