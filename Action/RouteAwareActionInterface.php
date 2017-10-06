<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * Common interface for actions with route management
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface RouteAwareActionInterface extends ActionInterface
{
    /**
     * @return string
     */
    public function getRoute(): string;

    /**
     * @param mixed
     *
     * @return array
     */
    public function getRouteParameters($object = null);
}
