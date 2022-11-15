<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Error\RuntimeError;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFilter;
use App\Entity\Event;
use Twig\TwigFunction;
use Twig\Extra\Intl;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('dateTime', [$this, 'formatDateTime']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(name: 'pluralize', callable: [$this, 'pluralize']),
            new TwigFunction(name: 'format_price', callable: [$this, 'format_price']),
        ];
    }


    public function pluralize(int $count, string $singular, ?string $plural = null): string
    {
        $plural ??= $singular . 's';
        $string = $count === 1 ? $singular : $plural;
        return "$count $string";
    }

    public function format_price(Event $event): string
    {
        return $event->isFree() ? 'Gratuit' : $event->getPrice() . ' €';
    }

    /**
     * @throws RuntimeError
     */
    public function formatDateTime(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format(format: 'F d, Y \a\t h:i A');
    }
}
