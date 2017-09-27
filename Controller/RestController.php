<?php

namespace Pim\Bundle\CustomEntityBundle\Controller;

use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RestController extends AbstractController
{
    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Return the list of registred references data
     *
     * @return JsonResponse
     */
    public function listAction()
    {
        $referenceDataNames = $this->registry->getNames();

        return new JsonResponse(array_combine($referenceDataNames, $referenceDataNames));
    }

    /**
     * Create a custom entity
     *
     * @param Request $request
     * @param string  $customEntityName
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, string $customEntityName): JsonResponse
    {
        $entity = $this->getManager($customEntityName)->create(
            $this->getEntityClass($customEntityName),
            json_decode($request->getContent(), true)
        );

        $this->getManager($customEntityName)->save($entity);

        $responseContent = [
            'customEntityName' => $customEntityName,
            'id'               => $entity->getId(),
        ];

        return new JsonResponse($responseContent);
    }

    /**
     * Removes custom entity
     *
     * @param string $customEntityName
     * @param int    $id
     *
     * @return JsonResponse
     */
    public function getAction(string $customEntityName, int $id): JsonResponse
    {
        $entity = $this->findEntity($customEntityName, $id);

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        $normalized = $this->normalize($customEntityName, $entity);

        return new JsonResponse($normalized);
    }

    /**
     * Removes custom entity
     *
     * @param string $customEntityName
     * @param int    $id
     *
     * @return JsonResponse
     */
    public function removeAction(string $customEntityName, int $id): JsonResponse
    {
        $entity = $this->findEntity($customEntityName, $id);

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        $this->getManager($customEntityName)->remove($entity);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function getManager(string $customEntityName): ManagerInterface
    {
        $configuration = $this->registry->get($customEntityName);
        $managerRegistry = $this->container->get('pim_custom_entity.manager.registry');

        return $managerRegistry->getFromConfiguration($configuration);
    }

    protected function getEntityClass(string $customEntityName): string
    {
        $configuration = $this->registry->get($customEntityName);

        return $configuration->getEntityClass();
    }

    protected function findEntity(string $customEntityName, int $id): ReferenceDataInterface
    {
        $manager = $this->getManager($customEntityName);
        $entity = $manager->find($this->getEntityClass($customEntityName), $id);

        return $entity;
    }

    protected function normalize(string $customEntityName, AbstractCustomEntity $entity): array
    {
        $manager = $this->getManager($customEntityName);
        $context = [
            'customEntityName' => $customEntityName,
            'form'             => sprintf('pim-%s-edit-form', $customEntityName),
        ];

        $normalized = $manager->normalize($entity, 'internal_api', $context);

        return $normalized;
    }
}
