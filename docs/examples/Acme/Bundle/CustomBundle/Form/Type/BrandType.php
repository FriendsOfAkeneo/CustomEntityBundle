<?php

namespace Acme\Bundle\CustomBundle\Form\Type;

use Pim\Bundle\CustomEntityBundle\Form\Type\CustomEntityType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This is an example of a form type for a custom entity.
 * It also shows how we can embed another custom entity in this form.
 *
 * In this case, it allows to select a fabric for this brand.
 *
 * @see http://symfony.com/doc/2.7/reference/forms/types/entity.html
 *
 * @author Remy Betus <remy.betus@akeneo.com>
 */
class BrandType extends CustomEntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

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
     * @return string
     */
    public function getName()
    {
        return 'acme_enrich_brand';
    }
}
