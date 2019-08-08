<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig\Compiler;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Node;

/**
 * Compiles the loop body of the list tag.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListLoopBodyNode extends Node
{

    /**
     * Creates a new ListLoopBodyNode instance.
     *
     * @param AssignNameExpression $key           The loop key variable name.
     * @param AssignNameExpression $value         The loop value variable name.
     * @param AssignNameExpression $arrayVariable The name of the variable in which the
     *                                            list content will be stored.
     * @param Node                 $body          The list body.
     * @param int                  $lineno
     * @param string               $tag
     */
    public function __construct(AssignNameExpression $key, AssignNameExpression $value, AssignNameExpression $arrayVariable, Node $body, $lineno, $tag)
    {
        parent::__construct(
            [
                'key'   => $key,
                'value' => $value,
                'var'   => $arrayVariable,
                'body'  => $body,
            ],
            [],
            $lineno,
            $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->write("ob_start();\n")->subcompile($this->getNode('body'))->subcompile($this->getNode('var'))->raw(
            "[")->subcompile($this->getNode('key'))->raw("] = ob_get_clean();\n");
    }
}
