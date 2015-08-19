<?php

namespace Pim\Bundle\CustomEntityBundle\EventListener\DataGrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;

/**
 * Inject custom entity configuration in pim_custom_entity grids
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigureCustomEntityGridListener
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @param ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Check whenever grid is flexible and add flexible columns dynamically
     *
     * @param BuildBefore $event
     *
     * @return null
     *
     * @throws \Exception
     */
    public function buildBefore(BuildBefore $event)
    {
        $datagridConfig = $event->getConfig();
        if ('custom_entity' !== $datagridConfig->offsetGetByPath('[extends]')) {
            return;
        }

        $indexAction = $this->actionFactory->getAction($datagridConfig->getName(), 'index');
        if (!$indexAction) {
            throw new \Exception(sprintf('No index action configured for %s', $datagridConfig->getName()));
        }

        $this->setMassActions($datagridConfig, $indexAction);
    }

    /**
     * Sets the mass actions
     *
     * @param DatagridConfiguration $datagridConfig
     * @param ActionInterface       $indexAction
     */
    protected function setMassActions(DatagridConfiguration $datagridConfig, ActionInterface $indexAction)
    {
        if ($indexAction->getConfiguration()->hasAction('mass_edit')) {
            $name = $indexAction->getConfiguration()->getName();
            $massAction = $this->actionFactory->getAction($name, 'mass_edit');

            $massActions = $datagridConfig->offsetGetByPath('[mass_actions]') ?: [];
            $massActions['mass_edit'] = $massAction->getGridActionOptions();
            $datagridConfig->offsetSetByPath('[mass_actions]', $massActions);
        }
    }
}
