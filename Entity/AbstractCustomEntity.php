<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Pim\Bundle\CatalogBundle\Model\ReferableInterface;

/**
 * Abstract custom entity
 *
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractCustomEntity implements ReferableInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * Returns the id
     *
     * @return int
     */
    public function getId()
    {
       return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getReference()
    {
        return $this->code;
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
     * @return AbstractCustomEntity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
