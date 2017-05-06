<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig_Compiler;
use Twig_Node;
use Twig_Node_Expression_AssignName as NodeAssingName;

/**
 * Compiles the loop body of the list tag.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListLoopBodyNode extends Twig_Node
{

    /**
     * Creates a new ListLoopBodyNode instance.
     *
     * @param NodeAssingName $key           The loop key variable name.
     * @param NodeAssingName $value         The loop value variable name.
     * @param NodeAssingName $arrayVariable The name of the variable in which the
     *                                      list content will be stored.
     * @param Twig_Node      $body          The list body.
     * @param int            $lineno
     * @param string         $tag
     */
    public function __construct(NodeAssingName $key, NodeAssingName $value, NodeAssingName $arrayVariable, Twig_Node $body, $lineno, $tag)
    {
        parent::__construct(
            array(
                'key'   => $key,
                'value' => $value,
                'var'   => $arrayVariable,
                'body'  => $body,
            ),
            array(),
            $lineno,
            $tag
        );
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->subcompile($this->getNode('var'))
            ->raw("[")
            ->subcompile($this->getNode('key'))
            ->raw("] = ob_get_clean();\n");
    }

}
