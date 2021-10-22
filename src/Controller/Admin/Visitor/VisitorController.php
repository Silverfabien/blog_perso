<?php

namespace App\Controller\Admin\Visitor;

use App\Repository\Visitor\VisitorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VisitorController
 */
class VisitorController extends AbstractController
{
    /**
     * @param VisitorRepository $visitorRepository
     * @return Response
     *
     * @Route("/admin/visitor", name="admin_visitor_index")
     */
    public function index(
        VisitorRepository $visitorRepository
    ): Response
    {
        return $this->render('admin/visitor/index.html.twig', [
            'visitors' => $visitorRepository->findAll()
        ]);
    }
}
