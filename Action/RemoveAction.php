<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remove action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RemoveAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute(Request $request, ConfigurationInterface $configuration, array $options)
    {
        $object = $this->findEntity($request, $configuration, $options);
        $this->manager->remove($object);

        return new Response('', 204);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return 'pim_customentity_remove';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'remove';
    }
}
