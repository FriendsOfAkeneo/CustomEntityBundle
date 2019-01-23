<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Versioning;

use Acme\Bundle\CustomBundle\Entity\Color;
use Akeneo\Tool\Bundle\VersioningBundle\Repository\VersionRepositoryInterface;
use Pim\Bundle\CustomEntityBundle\Tests\AbstractTestCase;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VersionableCustomEntityTest extends AbstractTestCase
{
    public function testUpdateVersionableEntity()
    {
        /** @var VersionRepositoryInterface $versionRepository */
        $versionRepository = $this->get('pim_versioning.repository.version');
        $data = [
            'code'  => 'blue',
            'name'  => 'Blue',
            'hex'   => '#0101FF',
            'red'   => 1,
            'green' => 1,
            'blue'  => 255,
        ];

        $blue = $this->createReferenceData(
            Color::class,
            $data
        );
        $blue = $this->manager->find(Color::class, $blue->getId());
        $data['id'] = $blue->getId();

        $versions = $versionRepository->getLogEntries(Color::class, $blue->getId());
        $this->assertCount(1, $versions);
        $this->assertEquals(1, $versions[0]->getVersion());
        $this->assertEquals($data, $versions[0]->getSnapshot());
        $expectedChangeset = array_map(function ($value) {
            return [
                'old' => '',
                'new' => $value,
            ];
        }, $data);
        $this->assertEquals($expectedChangeset, $versions[0]->getChangeset());
        $this->assertFalse($versions[0]->isPending());

        $changeset = [
            'name'  => 'Dark blue',
            'hex'   => '#0115AA',
            'green' => 21,
            'blue'  => 170,
        ];
        $this->manager->update($blue, $changeset);
        $this->manager->save($blue);

        $newest = $versionRepository->getNewestLogEntry(Color::class, $blue->getId());
        $expectedSnapshot = array_merge($data, $changeset);
        $expectedChangeset = [];
        foreach ($changeset as $key => $value) {
            $expectedChangeset[$key] = [
                'old' => $data[$key],
                'new' => $value,
            ];
        }

        $this->assertEquals(2, $newest->getVersion());
        $this->assertEquals($expectedSnapshot, $newest->getSnapshot());
        $this->assertEquals($expectedChangeset, $newest->getChangeset());
        $this->assertFalse($newest->isPending());
    }

    public function testDeleteVersionableEntity()
    {
        /** @var VersionRepositoryInterface $versionRepository */
        $versionRepository = $this->get('pim_versioning.repository.version');
        $data = [
            'code'  => 'blue',
            'name'  => 'Blue',
            'hex'   => '#0101FF',
            'red'   => 1,
            'green' => 1,
            'blue'  => 255,
        ];

        $blue = $this->createReferenceData(
            Color::class,
            $data
        );
        $blue = $this->manager->find(Color::class, $blue->getId());
        $resourceId = $blue->getId();
        $data['id'] = $resourceId;

        $versions = $versionRepository->getLogEntries(Color::class, $resourceId);
        $this->assertCount(1, $versions);
        $this->assertEquals(1, $versions[0]->getVersion());

        $this->manager->remove($blue);
        $this->assertNull($this->manager->find(Color::class, $resourceId));

        $versions = $versionRepository->getLogEntries(Color::class, $resourceId);
        $this->assertCount(2, $versions);

        $newest = $versionRepository->getNewestLogEntry(Color::class, $resourceId);
        $this->assertEquals(2, $newest->getVersion());
        $this->assertEquals('Deleted', $newest->getContext());
    }
}
