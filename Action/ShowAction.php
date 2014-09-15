<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * History action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ShowAction extends AbstractViewableAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'show';
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(['route' => 'pim_customentity_show']);
    }
}
