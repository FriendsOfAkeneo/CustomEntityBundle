<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * Interface for index actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface IndexActionInterface extends ActionInterface
{
    /**
     * Returns the type names of the mass actions
     *
     * @return string[]
     */
    public function getMassActions();

    /**
     * Returns the type names of the row actions
     *
     * @return string[]
     */
    public function getRowActions();
}
