<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property;

use Oro\Bundle\DataGridBundle\Datasource\ResultRecordInterface;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\UrlProperty as OroUrlProperty;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
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
    /** @var ActionFactory */
    protected $actionFactory;

    /** @var string */
    protected $customEntityName;

    /**
     * Constructor
     *
     * @param Router        $router
     * @param ActionFactory $actionFactory
     */
    public function __construct(Router $router, ActionFactory $actionFactory)
    {
        parent::__construct($router);
        $this->actionFactory = $actionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawValue(ResultRecordInterface $record)
    {
        list($this->customEntityName, $actionType) = explode('/', $this->get(self::ROUTE_KEY));
        $action = $this->actionFactory->getAction($this->customEntityName, $actionType);

        $route = $this->router->generate(
            $action->getRoute(),
            $this->getParameters($record) + $action->getRouteParameters(),
            $this->getOr(self::IS_ABSOLUTE_KEY, false)
        );

        return $route . $this->getOr(self::ANCHOR_KEY);
    }
}
