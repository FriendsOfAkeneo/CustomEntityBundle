<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

/**
 * Description of LocaleAwareRepositoryInterface
 * 
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
interface LocaleAwareRepositoryInterface
{
    /**
     * Sets the current locale
     * 
     * @param string $locale
     */
    public function setLocale($locale);
}
