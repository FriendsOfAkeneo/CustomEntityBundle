<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Jobs;

use Akeneo\Tool\Component\Batch\Model\JobInstance;
use Pim\Bundle\CustomEntityBundle\Tests\AbstractTestCase;
use Symfony\Component\Finder\Finder;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AbstractJobTestCase extends AbstractTestCase
{
    const DATA_FILE_PATH = __DIR__ . '/../Resources/data/';
    const EXPORT_PATH = '/tmp/test/export/';

    public function setUp(): void
    {
        parent::setUp();
        if (!file_exists(static::EXPORT_PATH)) {
            mkdir(static::EXPORT_PATH, 0755, true);
        }
    }

    public function tearDown(): void
    {
        $finder = new Finder();
        foreach ($finder->files()->in(static::EXPORT_PATH) as $file) {
            unlink($file);
        }
    }

    /**
     * @param string $code
     * @param string $type
     * @param string $refDataName
     * @param string $filepath
     */
    protected function createJobInstance($code, $type, $refDataName, $filepath = null)
    {
        $job = new JobInstance();
        $job->setCode($code);
        $job->setConnector('Reference data Connector');
        $job->setType($type);
        $job->setJobName(sprintf('csv_reference_data_%s', $type));

        $config = [
            'reference_data_name' => $refDataName,
        ];
        if (null !== $filepath) {
            $config['filePath'] = $filepath;
        }
        $job->setRawParameters($config);

        $this->get('akeneo_batch.saver.job_instance')->save($job);
    }

    /**
     * @param string $jobCode
     *
     * @return string
     */
    protected function launchExport(string $jobCode)
    {
        return $this->get('akeneo_integration_tests.launcher.job_launcher')->launchExport($jobCode);
    }

    /**
     * @param string $jobCode
     * @param string $content
     *
     * @return string
     */
    protected function launchImport(string $jobCode, string $content = '')
    {
        return $this->get('akeneo_integration_tests.launcher.job_launcher')->launchImport($jobCode, $content);
    }
}
