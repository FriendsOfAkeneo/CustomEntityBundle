<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class IndexAction extends AbstractViewableAction implements IndexActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        $vars = [];
        if ($this->configuration->hasAction('create')) {
            $vars['createUrl'] = $this->getActionUrl($this->options['quick_create_action_type']);
            $vars['quickCreate'] = $this->options['quick_create'];
        }

        return $this->renderResponse($vars);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'index';
    }

    /**
     * {@inheritdoc}
     */
    public function getMassActions()
    {
        return $this->options['mass_actions'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRowActions()
    {
        return $this->options['row_actions'];
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            [
                'route'                    => 'pim_customentity_index',
                'quick_create'             => false,
                'quick_create_action_type' => 'create',
                'template'                 => 'PimCustomEntityBundle:CustomEntity:index.html.twig',
                'row_actions'              => ['edit', 'delete'],
                'mass_actions'             => []
            ]
        );
    }
}
