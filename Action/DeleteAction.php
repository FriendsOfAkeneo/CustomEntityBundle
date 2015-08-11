<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeleteAction extends AbstractAction implements GridActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        $object = $this->findEntity($request);
        $this->getManager()->remove($object);

        return new Response('', 204);
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            [
                'route'               => 'pim_customentity_delete',
                'grid_action_options' => [
                    'type'      => 'delete',
                    'label'     => 'Delete',
                    'icon'      => 'trash'
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete';
    }

    /**
     * {@inheritdoc}
     */
    public function getGridActionOptions()
    {
        return $this->options['grid_action_options'];
    }
}
