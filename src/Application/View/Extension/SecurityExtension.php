<?php

namespace Glpi\Application\View\Extension;

use Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @since 10.0.0
 */
class SecurityExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_token', [Session::class, 'getNewCSRFToken']),
            new TwigFunction('idor_token', [Session::class, 'getNewIDORToken']),
        ];
    }
}
