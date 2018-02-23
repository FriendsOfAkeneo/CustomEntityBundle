<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Crud;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Tests\AbstractTestCase;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Builder\ProductBuilderInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\ProductInterface;

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
