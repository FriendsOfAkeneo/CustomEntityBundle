<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * Acme Fabric entity (used as multi reference data)
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Fabric extends AbstractCustomEntity
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $alternativeName;

    /** @var Collection of Color */
    protected $colors;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set alternativeName
     *
     * @param string $alternativeName
     */
    public function setAlternativeName($alternativeName)
    {
        $this->alternativeName = $alternativeName;
    }

    /**
     * Get alternativeName
     *
     * @return string
     */
    public function getAlternativeName()
    {
        return $this->alternativeName;
    }

    /**
     * @return Collection
     */
    public function getColors(): ?Collection
    {
        return $this->colors;
    }

    /**
     * @param Collection $colors
     *
     * @return $this
     */
    public function setColors(Collection $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @param Color $color
     *
     * @return $this
     */
    public function addColor(Color $color)
    {
        $this->colors->add($color);

        return $this;
    }

    /**
     * @param Color $color
     *
     * @return $this
     */
    public function removeColor(Color $color)
    {
        $this->colors->removeElement($color);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomEntityName(): string
    {
        return 'fabric';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSortOrderColumn(): string
    {
        return 'name';
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'name';
    }
}
