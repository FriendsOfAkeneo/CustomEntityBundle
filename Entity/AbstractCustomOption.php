<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Abstract custom option
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractCustomOption extends AbstractCustomEntity
{
    /**
     * @var string
     * 
     * @Assert\NotBlank
     */
    protected $label;

    /**
     * Get the label
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label
     * 
     * @param type $label
     * 
     * @return AbstractCustomOption
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Convert to string
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->label;
    }
}
