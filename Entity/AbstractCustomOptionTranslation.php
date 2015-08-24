<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Pim\Bundle\TranslationBundle\Entity\AbstractTranslation;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @deprecated will be removed in 1.7, please use \Pim\Bundle\TranslationBundle\Entity\AbstractTranslation
 */
class AbstractCustomOptionTranslation extends AbstractTranslation
{
    /**
     * @var string $label
     */
    protected $label;

    /**
     * @param string $label
     *
     * @return AbstractCustomOptionTranslation
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return AbstractCustomOptionTranslation
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->label;
    }
}
