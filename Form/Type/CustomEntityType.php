<?php

namespace Pim\Bundle\CustomEntityBundle\Form\Type;

use Pim\Bundle\EnrichBundle\Form\Subscriber\DisableFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This generic form must be extended by all custom entities.
 * It natively provides a read-only code field and can be used
 * independently.
 *
 * @author Rémy Bétus <remy.betus@akeneo.com>
 */
class CustomEntityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->addEventSubscriber(new DisableFieldSubscriber('code'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_custom_entity';
    }
}
