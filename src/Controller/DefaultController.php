<?php

namespace App\Controller;

use App\DependencyInjection\Jasper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/create", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param Jasper $jasper
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \PHPJasper\Exception\ErrorCommandExecutable
     * @throws \PHPJasper\Exception\InvalidCommandExecutable
     * @throws \PHPJasper\Exception\InvalidInputFile
     * @throws \PHPJasper\Exception\InvalidResourceDirectory
     */
    public function create(Request $request, Jasper $jasper)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('report');

        if (is_null($file)) {
            throw new NotFoundHttpException();
        }

        $jasper->create($file);

        return $this->json(
            [
                'message' => '',
                'path' => 'src/Controller/DefaultController.php',
            ]
        );
    }

    /**
     * @Route("/process/{name}", name="process", methods={"POST"})
     *
     * @param string $name
     * @param Jasper $jasper
     * @throws \PHPJasper\Exception\ErrorCommandExecutable
     * @throws \PHPJasper\Exception\InvalidCommandExecutable
     * @throws \PHPJasper\Exception\InvalidFormat
     * @throws \PHPJasper\Exception\InvalidInputFile
     * @throws \PHPJasper\Exception\InvalidResourceDirectory
     */
    public function process(string $name, Jasper $jasper)
    {
        $jasper->process($name);
    }
}
