<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Versioning\VersionableInterface;

/**
 * Acme Color entity (used as simple reference data)
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Color extends AbstractCustomEntity implements VersionableInterface
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $hex;

    /** @var int */
    protected $red;

    /** @var int */
    protected $green;

    /** @var int */
    protected $blue;

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
     * @return string
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * @param string $hex
     */
    public function setHex($hex)
    {
        $this->hex = $hex;
    }

    /**
     * @return int
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * @param int $red
     */
    public function setRed($red)
    {
        $this->red = $red;
    }

    /**
     * @return int
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * @param int $green
     */
    public function setGreen($green)
    {
        $this->green = $green;
    }

    /**
     * @return int
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * @param int $blue
     */
    public function setBlue($blue)
    {
        $this->blue = $blue;
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'name';
    }

    public function getCustomEntityName(): string
    {
        return 'color';
    }
}
