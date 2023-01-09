<?php

declare(strict_types=1);

namespace App\Controller\General;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $session = $request->getSession()->get(name: 'details_inscription');
        $request->getSession()->remove(name: 'details_inscription');
        return $this->render(view: 'homepage/index.html.twig');
    }
}
