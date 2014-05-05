<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

/**
 * Contains the name of all action events
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ActionEvents
{
    /**
     * Launched at action configuration
     */
    const CONFIGURE = 'pim_custom_entity.action.configure';

    /**
     * Launched before action execution
     */
    const PRE_EXECUTE = 'pim_custom_entity.action.pre_execute';

    /**
     * Launched at action configuration
     */
    const PRE_RENDER = 'pim_custom_entity.action.pre_render';

    /**
     * Launched after action execution
     */
    const POST_EXECUTE = 'pim_custom_entity.action.post_execute';
}
