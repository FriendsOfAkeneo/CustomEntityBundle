<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * Interface for mass actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface MassActionInterface
{
    /**
     * Returns the grid action type
     *
     * @return string
     */
    public function getGridType();

    /**
     * Returns the grid action label
     *
     * @return string
     */
    public function getGridLabel();

    /**
     * Returns the grid action icon
     *
     * @return string
     */
    public function getGridIcon();
}
