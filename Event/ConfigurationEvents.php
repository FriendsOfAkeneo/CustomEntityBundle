<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

/**
 * Contains the name of all configuration events
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigurationEvents
{
    /**
     * @staticvar string Launched at action configuration
     */
    const CONFIGURE = 'pim_custom_entity.configuration.configure';
}
