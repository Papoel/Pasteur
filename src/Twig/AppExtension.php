<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Event\Event;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter(name: 'dateTime', callable: [$this, 'formatDateTime']),
            new TwigFilter(name: 'html', callable: [$this, 'html'], options: ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(name: 'pluralize', callable: [$this, 'pluralize']),
            new TwigFunction(name: 'format_price', callable: [$this, 'formatPrice']),
        ];
    }

    // Create function for plural words
    public function pluralize(int $count, string $singular, string $plural): string
    {
        return $count > 1 ? $plural : $singular;
    }

    public function formatPrice(Event $event): string
    {
        $price = $event->getPrice() / 100;
        $price = number_format($price, 2);

        return $event->isFree() ? 'Gratuit' : $price . ' €';
    }

    public function formatDateTime(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format(format: 'F d, Y \a\t h:i A');
    }

    /**
     * @param $html
     *
     * @return mixed
     *
     * @description Supprime toutes les balises html d'une chaîne de caractères.
     */
    public function html($html)
    {
        return $html;
    }
}
