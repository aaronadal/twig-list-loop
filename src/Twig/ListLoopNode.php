<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\ForNode;

/**
 * Compiles the loop of the list tag.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListLoopNode extends ForNode
{

    /**
     * Creates a new ListLoopNode instance.
     *
     * @param AssignNameExpression $keyTarget
     * @param AssignNameExpression $valueTarget
     * @param AbstractExpression   $seq
     * @param AbstractExpression   $if
     * @param ListLoopBodyNode     $body
     * @param int                  $lineno
     * @param null                 $tag
     */
    public function __construct($keyTarget, $valueTarget, $seq, $if, $body, $lineno, $tag = null)
    {
        parent::__construct($keyTarget, $valueTarget, $seq, $if, $body, null, $lineno, $tag);
    }

}
