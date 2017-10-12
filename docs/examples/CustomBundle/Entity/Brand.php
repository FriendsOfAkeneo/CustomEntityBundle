<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class Brand extends AbstractCustomEntity
{
    /**
     * @var Fabric
     */
    protected $fabric;

    /**
     * @param Fabric|null $fabric
     *
     * @return Brand
     */
    public function setFabric(Fabric $fabric = null)
    {
        $this->fabric = $fabric;

        return $this;
    }

    /**
     * @return Fabric
     */
    public function getFabric()
    {
        return $this->fabric;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomEntityName(): string
    {
        return 'brand';
    }
}
