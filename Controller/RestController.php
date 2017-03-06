<?php


namespace Pim\Bundle\CustomEntityBundle\Controller;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 *
 * @author    Anaël Chardan <anael.chardan@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RestController
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return JsonResponse
     */
    public function listAction()
    {
        $referenceDataNames = $this->registry->getNames();

        return new JsonResponse(array_combine($referenceDataNames, $referenceDataNames));
    }
}
