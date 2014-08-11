<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Edit action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EditAction extends AbstractFormAction implements GridActionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getObject(Request $request)
    {
        return $this->findEntity($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return 'pim_customentity_edit';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'edit';
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
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(
            [
                'route'               => 'pim_customentity_edit',
                'success_message'     => sprintf('flash.%s.updated', $this->configuration->getName()),
                'grid_action_options' => [
                    'type'      => 'navigate',
                    'label'     => 'Edit',
                    'icon'      => 'edit',
                    'link'      => 'edit_link',
                    'rowAction' => true
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVars(Request $request, FormInterface $form)
    {
        $vars = parent::getTemplateVars($request, $form);
        if ($this->configuration->hasAction('delete')) {
            $vars['deleteUrl'] = $this->getActionUrl('delete', $form->getData());
        }

        return $vars;
    }
}
