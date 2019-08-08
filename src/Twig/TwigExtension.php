<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig\Extension\AbstractExtension;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class TwigExtension extends AbstractExtension
{

    public function getTokenParsers()
    {
        return array(
            new ListTokenParser(),
        );
    }
}
