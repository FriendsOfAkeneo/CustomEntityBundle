<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Abstract viewable action
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractViewableAction extends AbstractAction
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Constructor
     * 
     * @param ManagerInterface $manager
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param EngineInterface $templating
     */
    public function __construct(
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        EngineInterface $templating
    ) {
        parent::__construct($manager, $router, $translator);
        $this->templating = $templating;
    }

    /**
     * Renders a response
     * 
     * @param ConfigurationInterface $configuration
     * @param string $templateName
     * @param array $options
     * @param array $templateVars
     * 
     * @return Response
     */
    public function renderResponse(
        ConfigurationInterface $configuration,
        array $options,
        array $templateVars = []
    ) {
        return $this->templating->renderResponse(
            $options['template'],
            $templateVars + $this->getDefaultTemplateVars($configuration, $options)
        );
    }

    /**
     * Returns the default template vars
     * 
     * @param ConfigurationInterface $configuration
     * @param array                  $options
     * 
     * @return array
     */
    protected function getDefaultTemplateVars(ConfigurationInterface $configuration, array $options)
    {
        $vars = [
            'customEntityName' => $configuration->getName(),
            'baseTemplate'     => $options['base_template']
        ];

        if ($configuration->hasAction('index')) {
            $vars['indexUrl'] = $this->getActionUrl($configuration, 'index');
        }
        return $vars;
    }

    protected function setDefaultOptions(ConfigurationInterface $configuration, \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($configuration, $resolver);
        $resolver->setRequired(['template']);
        $resolver->setDefaults(['base_template' => 'PimCustomEntityBundle::layout.html.twig']);
    }
}
