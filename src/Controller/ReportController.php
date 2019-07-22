<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/report/{id}", name="report")
     * @param string $id
     * @param AdapterInterface $adapter
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function index(string $id, AdapterInterface $adapter)
    {
        dump($adapter);
        /** @var CacheItem $item */
        $item = $adapter->getItem($id);

        if (!$item->isHit()) {
            throw new NotFoundHttpException("Report does't exists");
        }

        $file = $item->get();

        return $this->json(
            [
                'message' => 'Welcome to your new controller!',
                'path' => 'src/Controller/ReportController.php',
            ]
        );
    }
}
