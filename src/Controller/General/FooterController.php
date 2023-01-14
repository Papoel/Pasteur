<?php

declare(strict_types=1);

namespace App\Controller\General;

use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    public function apeTeam(UserRepository $user): Response
    {
        $president = $user->findBy(criteria: ['function' => 'président']);
        $tresorier = $user->findBy(['function' => 'trésorier']);
        $secretaire = $user->findBy(['function' => 'secrétaire']);
        $webmaster = $user->findBy(['function' => 'webmaster']);

        return $this->render(view: 'components/Footer/_card-member.html.twig', parameters: [
            'president' => $president,
            'tresorier' => $tresorier,
            'secretaire' => $secretaire,
            'webmaster' => $webmaster,
        ]);
    }
}
