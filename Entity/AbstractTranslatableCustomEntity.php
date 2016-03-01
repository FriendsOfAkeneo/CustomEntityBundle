<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Akeneo\Component\Localization\Model\AbstractTranslation;
use Akeneo\Component\Localization\Model\TranslatableInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractTranslatableCustomEntity extends AbstractCustomEntity implements TranslatableInterface
{
    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addTranslation(AbstractTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation($locale = null)
    {
        $locale = ($locale) ? $locale : $this->locale;
        if (!$locale) {
            return null;
        }
        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLocale() == $locale) {
                return $translation;
            }
        }

        $translationClass = $this->getTranslationFQCN();
        $translation      = new $translationClass();
        $translation->setLocale($locale);
        $translation->setForeignKey($this);
        $this->addTranslation($translation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTranslation(AbstractTranslation $translation)
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $translation = $this->getTranslation();

        return ($translation && (string) $translation)
            ? (string) $translation
            : (string) $this->code;
    }
}
