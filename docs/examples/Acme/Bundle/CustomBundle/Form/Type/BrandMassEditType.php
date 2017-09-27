<?php

namespace Acme\Bundle\CustomBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class BrandMassEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fabric', 'entity', [
                'class'        => 'AcmeCustomBundle:Fabric',
                'choice_label' => 'name',
                'required'     => false,
                'placeholder'  => 'Choose a fabric',
                'empty_data'   => null
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['validation_groups' => ['mass_edit']]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'acme_enrich_brand_mass_edit';
    }
}
