<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Akeneo\Component\Localization\Model\TranslationInterface;
use Akeneo\Component\Localization\Model\AbstractTranslation;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class PictogramTranslation extends AbstractTranslation implements TranslationInterface
{
    /**
     * @var string $label
     */
    protected $label;

    /**
     * @param string $label
     *
     * @return TranslationInterface
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return TranslationInterface
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
