<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    public function apeTeam(UserRepository $user): Response
    {
        $president = $user->findByRole(role: "PRESIDENT");
        $tresorier = $user->findByRole(role: "TRESORIER");
        $secretaire = $user->findByRole(role: "SECRETAIRE");
        $administrateur = $user->findByRole(role: "ADMIN");

        return $this->render(view: 'components/Footer/_card-member.html.twig', parameters: [
            'president' => $president,
            'tresorier' => $tresorier,
            'secretaire' => $secretaire,
            'administrateur' => $administrateur
        ]);
    }
}
