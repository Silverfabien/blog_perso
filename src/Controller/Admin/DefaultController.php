<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 *
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * Page index
     *
     * @return Response
     *
     * @Route("/admin", name="admin_default")
     */
    public function index(): Response
    {
        return $this->render('admin/default/index.html.twig');
    }
}
