<?php

namespace Pim\Bundle\CustomEntityBundle\Controller;

use Akeneo\Component\FileStorage\PathGeneratorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Media REST controller
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Pim\Bundle\CustomEntityBundle\Controller
 */
class MediaRestController
{
    /**
     * Validator interface
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Path generator
     *
     * @var PathGeneratorInterface
     */
    protected $pathGenerator;

    /**
     * Upload directory
     *
     * @var string
     */
    protected $uploadDir;

    /**
     * @param ValidatorInterface     $validator
     * @param PathGeneratorInterface $pathGenerator
     * @param string                 $uploadDir
     */
    public function __construct(ValidatorInterface $validator, PathGeneratorInterface $pathGenerator, $uploadDir)
    {
        $this->validator     = $validator;
        $this->pathGenerator = $pathGenerator;
        $this->uploadDir     = $uploadDir;
    }

    /**
     * Post a new media and return original filename and path
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postAction(Request $request)
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file       = $request->files->get('file');
        $violations = $this->validator->validate($file);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = [
                    'message'       => $violation->getMessage(),
                    'invalid_value' => $violation->getInvalidValue(),
                ];
            }

            return new JsonResponse($errors, 400);
        }
        $pathData = $this->pathGenerator->generate($file);

        try {
            $movedFile = $file->move(
                $this->uploadDir . DIRECTORY_SEPARATOR . $pathData['path'] . DIRECTORY_SEPARATOR . $pathData['uuid'],
                $file->getClientOriginalName()
            );
        } catch (FileException $e) {
            return new JsonResponse("Unable to create target-directory, or moving file.", 400);
        }
        $filePath                 = $movedFile->getPathname();
        $filePathWithoutDirectory = str_replace($this->uploadDir . '/', '', $filePath);

        return new JsonResponse(
            [
                'originalFilename' => $file->getClientOriginalName(),
                'filePath'         => $filePath,
                'shortFilePath'    => $filePathWithoutDirectory
            ]
        );
    }
}
