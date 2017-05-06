<?php

namespace Aaronadal\TwigListLoop\Twig;


/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class TwigExtension extends \Twig_Extension
{

    public function getTokenParsers()
    {
        return array(
            new ListTokenParser(),
        );
    }

}
