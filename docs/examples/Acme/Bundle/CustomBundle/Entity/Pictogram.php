<?php

namespace Acme\Bundle\CustomBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

/**
 * @author Romain Monceau <romain@akeneo.com>
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
        return 'Acme\Bundle\CustomBundle\Entity\PictogramTranslation';
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
