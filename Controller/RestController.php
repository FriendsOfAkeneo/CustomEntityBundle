<?php

namespace Pim\Bundle\CustomEntityBundle\Controller;

use Pim\Bundle\CustomEntityBundle\Configuration\Registry as ConfigurationRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author    Anaël Chardan <anael.chardan@akeneo.com
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RestController
{
    /** @var ConfigurationRegistry */
    protected $configurations;

    public function __construct(ConfigurationRegistry $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * Return the list of registred references data
     *
     * @return JsonResponse
     */
    public function listAction()
    {
        $referenceDataNames = $this->configurations->getNames();

        return new JsonResponse(array_combine($referenceDataNames, $referenceDataNames));
    }
}
