<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

/**
 * Provides a mean to insert the locale in the repository, for pim_custom_entity datagrids
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
