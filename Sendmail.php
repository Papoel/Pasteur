<?php

declare(strict_types=1);

/**
 * Ce fichier est créé dans le but de pouvoir envoyer des emails en asynchrone
 * Ce fichier est appelé par une Cron Task au sein de mon hébergeur
 */

// Chemin vers le fichier console de Symfony
$consolePath = __DIR__ . '/bin/console';

// Commande à exécuter
$command = 'php ' . $consolePath . ' messenger:consume async';

exec(command: $command);
