<?php

namespace App\Controller\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OtherController
 */
class OtherController extends AbstractController
{
    /**
     * @return Response
     *
     * @Route("/mentions_legales", name="mentions_legales")
     */
    public function mentionsLegales(): Response
    {
        return $this->render('other/mentionsLegales.html.twig');
    }
}
