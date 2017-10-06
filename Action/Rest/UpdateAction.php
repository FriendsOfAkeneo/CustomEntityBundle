<?php

namespace Pim\Bundle\CustomEntityBundle\Action\Rest;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UpdateAction extends AbstractRestAction
{
    /** @var ValidatorInterface */
    protected $validator;

    /**
     * CreateAction constructor.
     *
     * @param ActionFactory      $actionFactory
     * @param ActionEventManager $eventManager
     * @param ManagerRegistry    $managerRegistry
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        ValidatorInterface $validator
    ) {
        parent::__construct($actionFactory, $eventManager, $managerRegistry);
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Request $request): JsonResponse
    {
        $data = $this->getDecodedContent($request->getContent());
        $entity = $this->findEntity($request);
        $manager = $this->getManager();
        $manager->update($entity, $data);

        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            throw new BadRequestHttpException('Invalid data');
        }

        $manager->save($entity);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'rest_update';
    }

    /**
     * Get the JSON decoded content. If the content is not a valid JSON, it throws an error 400.
     *
     * @param string $content content of a request to decode
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    protected function getDecodedContent($content)
    {
        $decodedContent = json_decode($content, true);

        if (null === $decodedContent) {
            throw new BadRequestHttpException('Invalid json payload received');
        }

        return $decodedContent;
    }
}
