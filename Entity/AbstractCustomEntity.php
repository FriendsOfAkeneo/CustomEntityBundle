<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Pim\Bundle\CatalogBundle\Model\ReferableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Abstract custom entity
 *
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @UniqueEntity(fields="code", message="This code is already taken")
 */
abstract class AbstractCustomEntity implements ReferableInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    protected $code;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $updated;

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

    /**
     * Get created time
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated time
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set created time
     *
     * @param \DateTime $created
     *
     * @return AbstractCustomEntity
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated time
     *
     * @param \DateTime $updated
     *
     * @return AbstractCustomEntity
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->code;
    }
}
