<?php

namespace Acme\Bundle\CustomBundle\Model;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Doctrine\Common\Collections\ArrayCollection;
use Pim\Component\Catalog\Model\ProductValue as PimProductValue;

/**
 * Override of the product value so it takes new custom entities into account.
 *
 * @author Rémy Bétus <remy.betus@akeneo.com>
 */
class ProductValue extends PimProductValue
{
    /** @var Color */
    protected $color;

    /** @var ArrayCollection */
    protected $fabrics;

    /** @var array (used only in MongoDB implementation) */
    protected $fabricIds;

    /** @var  Pictogram */
    protected $pictogram;

    /** @var  ArrayCollection */
    protected $brands;

    /**
     * @var array (used only on MongoDB implementation)
     * @todo: explain how
     */
    protected $brandIds;

    public function __construct()
    {
        parent::__construct();

        $this->fabrics = new ArrayCollection();
        $this->brands = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getFabrics()
    {
        return $this->fabrics;
    }

    /**
     * @param ArrayCollection $fabrics
     */
    public function setFabrics(ArrayCollection $fabrics)
    {
        $this->fabrics = $fabrics;
    }

    /**
     * @param Fabric $fabric
     */
    public function addFabric(Fabric $fabric)
    {
        $this->fabrics->add($fabric);
    }

    /**
     * @param Fabric $fabric
     */
    public function removeFabric(Fabric $fabric)
    {
        $this->fabrics->removeElement($fabric);
    }

    /**
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param Color $color
     */
    public function setColor(Color $color = null)
    {
        $this->color = $color;
    }

    /**
     * @return Pictogram
     */
    public function getPictogram()
    {
        return $this->pictogram;
    }

    /**
     * @param Pictogram $pictogram
     */
    public function setPictogram($pictogram)
    {
        $this->pictogram = $pictogram;
    }

    /**
     * @return ArrayCollection
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param ArrayCollection $brands
     */
    public function setBrands(ArrayCollection $brands)
    {
        $this->brands = $brands;
    }

    /**
     * @param Brand $brand
     */
    public function addBrand(Brand $brand)
    {
        $this->brands->add($brand);
    }

    /**
     * @param Brand $brand
     */
    public function removeBrand(Brand $brand)
    {
        $this->brands->remove($brand);
    }
}
