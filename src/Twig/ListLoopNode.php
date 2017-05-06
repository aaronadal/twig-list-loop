<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig_Node_Expression;
use Twig_Node_Expression_AssignName;

/**
 * Compiles the loop of the list tag.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListLoopNode extends \Twig_Node_For
{

    /**
     * Creates a new ListLoopNode instance.
     *
     * @param Twig_Node_Expression_AssignName $keyTarget
     * @param Twig_Node_Expression_AssignName $valueTarget
     * @param Twig_Node_Expression            $seq
     * @param Twig_Node_Expression            $if
     * @param ListLoopBodyNode                $body
     * @param Twig_Node_Expression            $lineno
     * @param null                            $tag
     */
    public function __construct($keyTarget, $valueTarget, $seq, $if, $body, $lineno, $tag = null)
    {
        parent::__construct($keyTarget, $valueTarget, $seq, $if, $body, null, $lineno, $tag);
    }

}
