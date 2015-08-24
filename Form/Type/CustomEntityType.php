<?php

namespace Pim\Bundle\CustomEntityBundle\Form\Type;

use Pim\Bundle\EnrichBundle\Form\Subscriber\DisableFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Rémy Bétus <remy.betus@akeneo.com>
 */
class CustomEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->addEventSubscriber(new DisableFieldSubscriber('code'));
    }

    public function getName()
    {
        return 'pim_enrich_custom_entity';
    }
}
