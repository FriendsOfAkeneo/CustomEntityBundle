<?php

namespace Pim\Bundle\CustomEntityBundle\Resolver;

use Pim\Bundle\CatalogBundle\Resolver\FQCNResolver as BaseResolver;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FQCNResolver extends BaseResolver
{
    public function getFQCN($entityType)
    {
        $className = parent::getFQCN($entityType);
        if (null === $className) {
            try {
                $className = $this->container->getParameter(sprintf('pim_custom_entity.entity.%s.class', $entityType));
            } catch (InvalidArgumentException $e) {
                $className = null;
            }
        }

        return $className;
    }
}
