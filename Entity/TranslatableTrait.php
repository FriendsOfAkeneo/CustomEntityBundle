<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Component\Localization\Model\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait for translatable entity
 *
 * @see       Akeneo\Component\Localization\Model\TranslatableInterface
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
trait TranslatableTrait
{
    /** @var ArrayCollection */
    protected $translations;

    /** @var string */
    protected $locale;

    /**
     * Gets translation for current locale
     *
     * @param null|string $locale
     *
     * @return TranslatableTrait
     */
    public function getTranslation(?string $locale = null)
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
        $translation = new $translationClass();
        $translation->setLocale($locale);
        $translation->setForeignKey($this);
        $this->addTranslation($translation);

        return $translation;
    }

    /**
     * Gets translations
     *
     * @return ArrayCollection
     */
    public function getTranslations(): ArrayCollection
    {
        return $this->translations;
    }

    /**
     * Adds translation
     *
     * @param TranslationInterface $translation
     *
     * @return TranslatableTrait
     */
    public function addTranslation(TranslationInterface $translation): TranslatableTrait
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
        }

        return $this;
    }

    /**
     * Removes translation
     *
     * @param TranslationInterface $translation
     *
     * @return TranslatableTrait
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    /**
     * Gets translation full qualified class name
     *
     * @return string
     */
    abstract public function getTranslationFQCN();

    /**
     * Sets the locale used for translation
     *
     * @param string $locale
     *
     * @return TranslatableTrait
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
