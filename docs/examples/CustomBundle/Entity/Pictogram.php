<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

/**
 * @author Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pictogram extends AbstractTranslatableCustomEntity
{
    /**
     * Get translation full qualified class name
     *
     * @return string
     */
    public function getTranslationFQCN(): string
    {
        return PictogramTranslation::class;
    }

    /**
     * Returns the custom entity name used in the configuration
     * Used to map row actions on datagrid
     *
     * @return string
     */
    public function getCustomEntityName(): string
    {
        return 'pictogram';
    }
}
