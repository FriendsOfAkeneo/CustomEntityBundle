<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

/**
 * @author     Antoine Guigan <antoine@akeneo.com>
 * @copyright  2013 Akeneo SAS (http://www.akeneo.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @deprecated will be removed in 1.7,
 *             please use \Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity
 */
abstract class AbstractTranslatableCustomOption extends AbstractTranslatableCustomEntity
{
    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        $translated = $this->getTranslation() ? $this->getTranslation()->getLabel() : null;

        return ($translated !== '' && $translated !== null) ? $translated : sprintf('[%s]', $this->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel(string $label): AbstractTranslatableCustomEntity
    {
        $this->getTranslation()->setLabel($label);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSortOrderColumn(): string
    {
        return 'label';
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'label';
    }
}
