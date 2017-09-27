<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

/**
 * @author     Antoine Guigan <antoine@akeneo.com>
 * @copyright  2013 Akeneo SAS (http://www.akeneo.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @deprecated will be removed in 1.7, please use \Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity
 */
abstract class AbstractCustomOption extends AbstractCustomEntity
{
    /** @var string */
    protected $label;

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return AbstractCustomOption
     */
    public function setLabel($label): AbstractCustomOption
    {
        $this->label = $label;

        return $this;
    }
}
