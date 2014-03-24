<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Edit action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EditAction extends AbstractFormAction
{
    /**
     * {@inheritdoc}
     */
    protected function getObject(Request $request, ConfigurationInterface $configuration, array $options)
    {
        return $this->findEntity($request, $configuration, $options);
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
    protected function setDefaultOptions(ConfigurationInterface $configuration, OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($configuration, $resolver);
        $resolver->setDefaults(
            [
                'success_message' => sprintf('flash.%s.updated', $configuration->getName())
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVars(Request $request, ConfigurationInterface $configuration, Form $form, array $options)
    {
        $vars = parent::getTemplateVars($request, $configuration, $form, $options);
        if ($configuration->hasAction('remove')) {
            $vars['deleteUrl'] = $this->getActionUrl($configuration, 'remove', $form->getData());
        }

        return $vars;
    }
}
