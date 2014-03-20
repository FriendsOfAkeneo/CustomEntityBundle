<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Pager;

use Oro\Bundle\DataGridBundle\Datagrid\Builder;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Pim\Bundle\CustomEntityBundle\Datasource\Orm\CustomEntityDatasource;
use Pim\Bundle\DataGridBundle\Extension\Pager\OrmPagerExtension;

/**
 * Description of CustomEntityPagerExtension
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CustomEntityPagerExtension extends OrmPagerExtension
{
    public function isApplicable(DatagridConfiguration $config)
    {
        $datasourceType = $config->offsetGetByPath(Builder::DATASOURCE_TYPE_PATH);

        return $datasourceType == CustomEntityDatasource::TYPE;
    }
}
