<?php

namespace Pim\Bundle\CustomEntityBundle\Remover;

use Akeneo\Bundle\StorageUtilsBundle\Doctrine\Common\Remover\BaseRemover;
use Doctrine\Common\Persistence\ObjectManager;
use Pim\Bundle\CustomEntityBundle\Checker\ProductLinkCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class NonRemovableEntityException extends \RuntimeException
{
}
