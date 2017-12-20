<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Remover\NonRemovableEntityException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeleteAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        $object = $this->findEntity($request);
        try {
            $this->getManager()->remove($object);
        } catch (NonRemovableEntityException $e) {
            return new Response($e->getMessage(), 412);
        }

        return new Response('', 204);
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(['route' => 'pim_customentity_delete']);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete';
    }
}
