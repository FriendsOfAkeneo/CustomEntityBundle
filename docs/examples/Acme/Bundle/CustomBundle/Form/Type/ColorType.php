<?php

namespace Acme\Bundle\CustomBundle\Form\Type;

use Pim\Bundle\CustomEntityBundle\Form\Type\CustomEntityType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class ColorType extends CustomEntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name')
            ->add('hex')
            ->add('red')
            ->add('green')
            ->add('blue')
            ->add('hue')
            ->add('hslSaturation')
            ->add('light')
            ->add('hsvSaturation')
            ->add('value')
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acme_enrich_color';
    }
}
