<?php

namespace Pim\Bundle\CustomEntityBundle\Form\Type;

use Pim\Bundle\CustomEntityBundle\Form\Subscriber\ClearMissingSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Box collection
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ClearMissingSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_customentity_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }
}
