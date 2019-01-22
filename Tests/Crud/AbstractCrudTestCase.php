<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Pim\Bundle\CustomEntityBundle\Tests\AbstractTestCase;
use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Pim\Enrichment\Component\Product\Builder\ProductBuilderInterface;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractCrudTestCase extends AbstractTestCase
{
    /**
     * @param string $code
     * @param string $refDataName
     * @param string $type
     *
     * @return AttributeInterface
     */
    protected function createRefDataAttribute(
        $code,
        $refDataName,
        $type = AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT
    ): AttributeInterface {
        $attribute = $this->get('pim_catalog.factory.attribute')->create();

        $data = [
            'code' => $code,
            'type' => $type,
            'reference_data_name' => $refDataName,
        ];
        $this->get('pim_catalog.updater.attribute')->update($attribute, $data);
        $this->get('pim_catalog.saver.attribute')->save($attribute);

        return $attribute;
    }

    /**
     * @param string $identifier
     * @param AttributeInterface[] $attributes
     * @param array $data
     */
    protected function createProduct($identifier, $data): ProductInterface
    {
        /** @var ProductBuilderInterface $builder */
        $builder = $this->get('pim_catalog.builder.product');
        $attrRepo = $this->get('pim_catalog.repository.attribute');

        $product = $builder->createProduct($identifier);

        foreach ($data as $attrCode => $values) {
            $attribute = $attrRepo->findOneByIdentifier($attrCode);
            $builder->addAttribute($product, $attribute);
            $builder->addOrReplaceValue($product, $attribute, null, null, $values);
        }

        $this->get('pim_catalog.saver.product')->save($product);

        return $product;
    }
}
