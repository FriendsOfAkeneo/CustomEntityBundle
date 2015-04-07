<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Pim\Bundle\TranslationBundle\Entity\AbstractTranslation;
use Pim\Bundle\TranslationBundle\Entity\TranslatableInterface;

/**
 * Abstract custom entity
 *
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractTranslatableCustomEntity extends AbstractCustomEntity implements TranslatableInterface
{
    /** @var ArrayCollection */
    protected $translations;

    /** @var string */
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
            $translation->setForeignKey($this);
            $this->translations->add($translation);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation()
    {
        if (null === $this->locale) {
            return;
        }

        foreach ($this->translations as $translation) {
            if ($this->locale === $translation->getLocale()) {
                return $translation;
            }
        }

        $class = $this->getTranslationFQCN();
        $translation = new $class;
        $translation->setLocale($this->locale);
        $this->addTranslation($translation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTranslation(AbstractTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $translation = $this->getTranslation();

        return ($translation && (string) $translation)
            ? (string) $translation
            : (string) $this->code;
    }
}
