<?php

namespace Pim\Bundle\CustomEntityBundle\Remover;

use Akeneo\Bundle\StorageUtilsBundle\Doctrine\Common\Remover\BaseRemover;
use Doctrine\Common\Persistence\ObjectManager;
use Pim\Bundle\CustomEntityBundle\Checker\ProductLinkCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base remover, declared as different services for different classes
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityRemover extends BaseRemover
{
    /** @var ProductLinkCheckerInterface */
    protected $productLinkChecker;

    public function __construct(
        ObjectManager $objectManager,
        EventDispatcherInterface $eventDispatcher,
        $removedClass,
        ProductLinkCheckerInterface $productLinkChecker
    ) {
        parent::__construct($objectManager, $eventDispatcher, $removedClass);
        $this->productLinkChecker = $productLinkChecker;
    }

    protected function validateObject($entity)
    {
        parent::validateObject($entity);

        if ($this->productLinkChecker->isLinkedToProduct($entity)) {
            throw new NonRemovableEntityException('Cannot remove this entity because it used in one or more product.');
        }
    }
}
