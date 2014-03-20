<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Filter;

use Oro\Bundle\DataGridBundle\Datagrid\Builder;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Pim\Bundle\CustomEntityBundle\Datasource\Orm\CustomEntityDatasource;
use Pim\Bundle\DataGridBundle\Extension\Filter\OrmFilterExtension;

/**
 * Applies orm data source rules for the pim_custom_entity datasource
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityFilterExtension extends OrmFilterExtension
{
    /**
     * {@inheritdoc}
     */
    public function isApplicable(DatagridConfiguration $config)
    {
        $datasourceType = $config->offsetGetByPath(Builder::DATASOURCE_TYPE_PATH);

        return $datasourceType == CustomEntityDatasource::TYPE;
    }
}
