<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

/**
 * History action
 *
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

    protected function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(
            [
                'template' => 'PimCustomEntityBundle:CustomEntity:_history.html.twig',
                'route'    => 'pim_customentity_history'
            ]
        );
    }
}
