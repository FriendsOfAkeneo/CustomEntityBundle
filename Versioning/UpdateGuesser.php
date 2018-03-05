<?php

namespace Pim\Bundle\CustomEntityBundle\Versioning;

use Doctrine\ORM\EntityManager;
use Pim\Bundle\VersioningBundle\UpdateGuesser\UpdateGuesserInterface;

/**
 * Custom entity update guesser
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UpdateGuesser implements UpdateGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportAction($action)
    {
        return in_array(
            $action,
            [UpdateGuesserInterface::ACTION_UPDATE_ENTITY]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function guessUpdates(EntityManager $em, $entity, $action)
    {
        $pendings = [];
        if ($entity instanceof VersionableInterface) {
            $pendings[] = $entity;
        }

        return $pendings;
    }
}
