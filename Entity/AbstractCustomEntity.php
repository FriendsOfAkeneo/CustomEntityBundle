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
     * {@inheritdoc}
     */
    public function getReference()
    {
        return $this->code;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
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
     * Returns the custom entity name used in the configuration
     * Used to map row actions on datagrid
     *
     * @return string
     */
    public function getCustomEntityName() {
        return strtolower(join('', array_slice(explode('\\', __CLASS__), -1)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty()
    {
        return 'code';
    }

    /**
     * Returns the sort order
     *
     * @return string
     */
    public static function getSortOrderColumn()
    {
        return 'code';
    }
}
