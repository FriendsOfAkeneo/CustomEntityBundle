<?php

namespace Pim\Bundle\CustomEntityBundle\AttributeType;

use Pim\Bundle\CatalogBundle\AttributeType\OptionSimpleSelectType;
use Pim\Bundle\CatalogBundle\Model\ProductValueInterface;
use Pim\Bundle\CatalogBundle\Validator\ConstraintGuesserInterface;

/**
 * Custom option multiple select attribute type
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomOptionSimpleSelectType extends OptionSimpleSelectType
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $optionClass;

    /**
     * Constructor
     *
     * @param ConstraintGuesserInterface $constraintGuesser
     * @param string                     $formType
     * @param string                     $name
     * @param string                     $backendType
     * @param string                     $optionClass
     */
    public function __construct(
        $backendType,
        $formType,
        ConstraintGuesserInterface $constraintGuesser,
        $name,
        $optionClass
    ) {
        parent::__construct($backendType, $formType, $constraintGuesser);
        $this->name = $name;
        $this->optionClass = $optionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValueFormOptions(ProductValueInterface $value)
    {
        return [
                'class' => $this->optionClass
            ] + parent::prepareValueFormOptions($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
