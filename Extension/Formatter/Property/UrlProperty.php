<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property;

use Oro\Bundle\DataGridBundle\Datasource\ResultRecordInterface;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\UrlProperty as OroUrlProperty;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\Routing\Router;

/**
 * Overriden UrlProperty class
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UrlProperty extends OroUrlProperty
{
    /**
     * @var Registry
     */
    protected $configurationRegistry;

    /**
     * @var string
     */
    protected $customEntityName;

    /**
     * 
     * @param Router $router
     * @param Registry $configurationRegistry
     */
    public function __construct(Router $router, Registry $configurationRegistry)
    {
        parent::__construct($router);
        $this->configurationRegistry = $configurationRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawValue(ResultRecordInterface $record)
    {
        list($this->customEntityName, $actionName) = explode('/', $this->get(self::ROUTE_KEY));

        $configuration = $this->configurationRegistry->get($this->customEntityName);
        $action = $configuration->getAction($actionName);

        $route = $this->router->generate(
            $action->getRoute(),
            $this->getParameters($record) + $action->getRouteParameters($configuration),
            $this->getOr(self::IS_ABSOLUTE_KEY, false)
        );

        return $route . $this->getOr(self::ANCHOR_KEY);
    }

}
