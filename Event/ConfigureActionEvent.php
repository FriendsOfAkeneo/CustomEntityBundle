<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Event for action configuration
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigureActionEvent extends ActionEvent
{
    /**
     * @var OptionsResolverInterface
     */
    protected $optionsResolver;

    /**
     * @param ActionInterface          $action
     * @param OptionsResolverInterface $optionsResolver
     */
    public function __construct(ActionInterface $action, OptionsResolverInterface $optionsResolver)
    {
        parent::__construct($action);
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * Returns the options resolver for the action
     *
     * @return OptionsResolverInterface
     */
    public function getOptionsResolver()
    {
        return $this->optionsResolver;
    }
}
