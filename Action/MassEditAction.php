<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Batch edit action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MassEditAction extends CreateAction implements MassActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'mass_edit';
    }

    /**
     * {@inheritdoc}
     */
    public function getGridIcon()
    {
        return $this->options['grid_icon'];
    }

    /**
     * {@inheritdoc}
     */
    public function getGridLabel()
    {
        return $this->options['grid_label'];
    }

    /**
     * {@inheritdoc}
     */
    public function getGridType()
    {
        return $this->options['grid_type'];
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(
            [
                'grid_type' => 'redirect',
                'grid_label'=> 'Mass Edit',
                'grid_icon' => 'edit',
                'route'     => 'pim_customentity_batchedit'
            ]
        );
    }
}
