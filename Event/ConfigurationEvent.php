<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Launched upon custom entity options configuration
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigurationEvent extends Event
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * Constructor
     *
     * @param ConfigurationInterface $configuration
     * @param OptionsResolver        $optionsResolver
     */
    public function __construct(ConfigurationInterface $configuration, OptionsResolver $optionsResolver)
    {
        $this->configuration = $configuration;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * Returns the configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the options resolver
     *
     * @return OptionsResolver
     */
    public function getOptionsResolver()
    {
        return $this->optionsResolver;
    }
}
