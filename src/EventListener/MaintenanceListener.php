<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener
{
    private $maintenance;
    private Environment $twig;

    public function __construct($maintenance, Environment $twig)
    {
        $this->maintenance = $maintenance;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        // Si le site est en maintenance
        if (!file_exists($this->maintenance)) {
            // Ne rien faire
            return;
        }
        // Le fichier existe, le site est en maintenance
        // Définir la réponse qui sera envoyée
        $requestEvent->setResponse(
            response: new Response(
                // content: 'Le site est en maintenance.',
                // Send template maintenance.html.twig
                content:$this->twig->render(name: 'maintenance/maintenance.html.twig'),
                status: Response::HTTP_SERVICE_UNAVAILABLE,
                // HEADERS accept template maintenance.html.twig
                headers: [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ],
            )
        );
        // Arrêter la propagation des événements
        $requestEvent->stopPropagation();
    }
}
