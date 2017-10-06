<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

/**
 * Trait for translation entities
 *
 * @see       Akeneo\Component\Localization\Model\TranslationInterface
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
trait TranslationTrait
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $locale;

    /** @var int */
    protected $foreignKey;

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets locale
     *
     * @param string $locale
     *
     * @return TranslationTrait
     */
    public function setLocale($locale): TranslationTrait
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets locale
     *
     * @return string $locale
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Sets foreignKey
     *
     * @param int $foreignKey
     *
     * @return TranslationTrait
     */
    public function setForeignKey($foreignKey): TranslationTrait
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    /**
     * Gets foreignKey
     *
     * @return int $foreignKey
     */
    public function getForeignKey(): int
    {
        return $this->foreignKey;
    }
}
