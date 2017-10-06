<?php

namespace Acme\Bundle\CustomBundle\Entity;

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
     * Set year
     *
     * @param int $year
     */
    public function setAlternativeName($year)
    {
        $this->alternativeName = $year;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getAlternativeName()
    {
        return $this->alternativeName;
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
