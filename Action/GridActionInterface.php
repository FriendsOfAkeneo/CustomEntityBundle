<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * Interface for mass actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface GridActionInterface
{
    /**
     * Returns the grid action options
     *
     * @return array
     */
    public function getGridActionOptions();
}
