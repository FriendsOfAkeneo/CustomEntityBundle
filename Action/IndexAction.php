<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\AbstractViewableAction;
use Pim\Bundle\CustomEntityBundle\Action\QuickCreateAction;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Index action
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class IndexAction extends AbstractViewableAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute(Request $request, ConfigurationInterface $configuration, array $options)
    {
        $vars = [];
        if ($configuration->hasAction('create')) {
            $vars['createUrl'] = $this->getActionUrl($configuration, 'create');
            $vars['quickCreate'] = $configuration->getAction('create') instanceof QuickCreateAction;
        }
        return $this->renderResponse($configuration, $options, $vars);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return 'pim_customentity_index';
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
    protected function setDefaultOptions(ConfigurationInterface $configuration, OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($configuration, $resolver);
        $resolver->setDefaults(['template' => 'PimCustomEntityBundle:CustomEntity:index.html.twig']);
    }
}
