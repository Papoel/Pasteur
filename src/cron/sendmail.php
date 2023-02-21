<?php

declare(strict_types=1);

namespace App\Controller\cron;

// Chemin vers le fichier console de Symfony
$consolePath = __DIR__ . '/bin/console';

// Commande à exécuter
$command = 'php ' . $consolePath . ' messenger:consume async';

exec(command: $command);
