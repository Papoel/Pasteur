<?php

declare(strict_types=1);

namespace App\Cron;

final class sendmail
{
    public function sendEmailWithCron(): void
    {
        // Chemin vers le fichier console de Symfony
        $consolePath = __DIR__ . '/bin/console';

        // Commande à exécuter
        $command = 'php ' . $consolePath . ' messenger:consume async';

        exec(command: $command);
    }
}
