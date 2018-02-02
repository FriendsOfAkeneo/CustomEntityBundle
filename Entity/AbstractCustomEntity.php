<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Pim\Component\ReferenceData\Model\AbstractReferenceData;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractCustomEntity extends AbstractReferenceData
{
    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    /**
     * @return \DateTime
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $created
     *
     * @return AbstractCustomEntity
     */
    public function setCreated(\DateTime $created): AbstractCustomEntity
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @param \DateTime $updated
     *
     * @return AbstractCustomEntity
     */
    public function setUpdated(\DateTime $updated): AbstractCustomEntity
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Returns the custom entity name used in the configuration
     * Used to map row actions on datagrid
     *
     * @return string
     */
    public function getCustomEntityName(): string
    {
        return strtolower(join('', array_slice(explode('\\', get_class($this)), -1)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'code';
    }

    /**
     * Returns the sort order
     *
     * @return string
     */
    public static function getSortOrderColumn(): string
    {
        return 'code';
    }
}
