<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MassDeleteAction extends AbstractAction implements GridActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        throw new \Exception('This action is not executable');
    }

    /**
     * {@inheritdoc}
     */
    public function getGridActionOptions()
    {
        return $this->options['grid_action_options'];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'mass_delete';
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'route' => null,
                'grid_action_options' => [
                    'type' => 'delete',
                    'label' => 'Delete',
                    'entity_name' => $this->configuration->getName(),
                    'data_identifier' => 'o',
                    'launcherOptions' => [
                        'icon' => 'trash'
                    ]
                ]
            ]
        );
    }
}
