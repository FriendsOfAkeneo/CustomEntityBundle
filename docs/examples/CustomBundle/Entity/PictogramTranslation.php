<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Akeneo\Tool\Component\Localization\Model\AbstractTranslation;
use Akeneo\Tool\Component\Localization\Model\TranslationInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class PictogramTranslation extends AbstractTranslation implements TranslationInterface, TranslatableCustomEntityInterface
{
    /**
     * @var string $label
     */
    protected $label;

    /**
     * @param string $label
     *
     * @return TranslatableCustomEntityInterface
     */
    public function setLabel(string $label): TranslatableCustomEntityInterface
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->label;
    }
}
