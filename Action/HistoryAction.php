<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class HistoryAction extends AbstractViewableAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'history';
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(
            [
                'template'      => 'PimCustomEntityBundle:CustomEntity:_history.html.twig',
                'route'         => 'pim_customentity_history',
                'datagrid_name' => 'history-grid'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultTemplateVars()
    {
        return parent::getDefaultTemplateVars() + ['datagridName' => $this->options['datagrid_name']];
    }
}
