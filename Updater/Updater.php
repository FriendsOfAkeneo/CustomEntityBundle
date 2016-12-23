<?php

namespace Pim\Bundle\CustomEntityBundle\Updater;

use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Util\Inflector;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class Updater implements ObjectUpdaterInterface
{
    /**
     * {@inheritdoc}
     */
    public function update($referenceData, array $data, array $options = [])
    {
        if (!$referenceData instanceof ReferenceDataInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects a "%s", "%s" provided',
                    'Pim\Component\ReferenceData\Model\ReferenceDataInterface',
                    ClassUtils::getClass($referenceData)
                )
            );
        }

        foreach ($data as $field => $value) {
            $this->setData($referenceData, $field, $value);
        }

        return $this;
    }

    /**
     * @param ReferenceDataInterface $referenceData
     * @param string $field
     * @param mixed $value
     */
    protected function setData(ReferenceDataInterface $referenceData, $field, $value)
    {
        $method = 'set'. Inflector::classify($field);
        $referenceData->$method($value);
    }
}
