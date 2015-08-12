<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PreRenderActionEvent extends ActionEvent
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $templateVars;

    /**
     * @param ActionInterface $action
     * @param string          $template
     * @param array           $templateVars
     */
    public function __construct(ActionInterface $action, $template, array $templateVars)
    {
        parent::__construct($action);

        $this->template = $template;
        $this->templateVars = $templateVars;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getTemplateVars()
    {
        return $this->templateVars;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param array $templateVars
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
    }
}
