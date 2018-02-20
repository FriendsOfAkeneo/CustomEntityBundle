<?php

namespace Pim\Bundle\CustomEntityBundle\Tests;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AppKernelTest extends \AppKernel
{
    /**
     * {@inheritdoc}
     */
    protected function registerProjectBundles(): array
    {
        return [
            new \Akeneo\Test\IntegrationTestsBundle\AkeneoIntegrationTestsBundle(),
            new \Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
            new \Acme\Bundle\CustomBundle\AcmeCustomBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        if (null === $this->name) {
            $this->name = parent::getName() . '_test';
        }

        return $this->name;
    }
}
