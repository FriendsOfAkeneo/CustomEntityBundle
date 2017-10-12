<?php

namespace Acme\Bundle\CustomBundle\Entity;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface TranslatableCustomEntityInterface
{
    /**
     * @param string $label
     *
     * @return TranslatableCustomEntityInterface
     */
    public function setLabel(string $label): TranslatableCustomEntityInterface;

    /**
     * @return string
     */
    public function getLabel():string;
}
