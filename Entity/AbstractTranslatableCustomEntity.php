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
    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var AbstractTranslation
     */
    private $translation;

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
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        foreach ($this->translations as $translation) {
            if ($locale === $translation->getLocale()) {
                $this->translation = $translation;
            }
        }

        if (null === $this->translation) {
            $class = $this->getTranslationFQCN();
            $this->translation = new $class;
            $this->translation->setLocale($locale);
            $this->addTranslation($this->translation);
        }
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return AbstractTranslatableCustomEntity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
