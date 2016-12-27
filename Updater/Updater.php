<?php

namespace Pim\Bundle\CustomEntityBundle\Updater;

use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Util\Inflector;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Reference data updater
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Updater implements ObjectUpdaterInterface
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

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

        foreach ($data as $propertyPath => $value) {
            $this->propertyAccessor->setValue($referenceData, $propertyPath, $value);
        }

        return $this;
    }
}
