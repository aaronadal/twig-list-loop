<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig\Compiler;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Node;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListNode extends Node
{

    /**
     * Creates a new ListingNode instance.
     *
     * @param Node                 $template   The template to display the listing
     * @param AssignNameExpression $listTarget The list variable name
     * @param ListLoopNode         $listLoop   The list loop node
     * @param Node|null            $args       The additional args for the template
     * @param Node|null            $else       The else template to render when no results
     * @param int                  $lineno     The line number
     * @param string               $tag        The tag
     */
    public function __construct($template, $listTarget, $listLoop, $args, $else, $lineno, $tag)
    {
        $nodes = [
            'template'   => $template,
            'listTarget' => $listTarget,
            'listLoop'   => $listLoop,
        ];

        if($else !== null) {
            $nodes['else'] = $else;
        }

        if($args !== null) {
            $nodes['args'] = $args;
        }

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        // Store the loop result in the listTarget variable.
        $compiler->write("\n")->subcompile($this->getNode('listTarget'))->raw(" = array();\n")->subcompile(
                $this->getNode('listLoop'))->raw("\n");

        // Store the else result in the $else variable.
        if($this->hasNode('else')) {
            $compiler->write("ob_start();\n")->subcompile($this->getNode('else'))->write("\n")->write(
                    "\$else = ob_get_clean();\n\n");
        }
        else {
            $compiler->write("\$else = '';\n\n");
        }

        // Store the additional arguments in the $args variable.
        if($this->hasNode('args')) {
            $compiler->write("\$args = ")->subcompile($this->getNode('args'))->raw(";\n\n");
        }
        else {
            $compiler->write("\$args = array();\n\n");
        }

        // Put the additional args in the $context.
        $compiler->write("foreach(\$args as \$argKey => \$argVal):\n")->indent()->write(
                "\$context[\$argKey] = \$argVal;\n")->outdent()->write("endforeach;\n\n");

        // Put the list and the else in the context.
        $compiler->write("\$context['list'] = ")->subcompile($this->getNode('listTarget'))->raw(";\n")->write(
                "\$context['else'] = \$else;\n\n");

        // Render the list template.
        $compiler->write('$this->loadTemplate(')->subcompile($this->getNode('template'))->raw(', ')->repr(
                $this->getTemplateName())->raw(', ')->repr($this->getTemplateLine())->raw(')')->write(
                "->display(\$context);\n\n");

        // Unset the additional arguments from the context.
        $compiler->write("foreach(\$args as \$argKey => \$argVal):\n")
            ->indent()
            ->write("unset(\$context[\$argKey]);\n")
            ->outdent()
            ->write("endforeach;\n\n");

        // Unset the rest of variables.
        $compiler->write("unset(\$args);\n")->write("unset(")->subcompile($this->getNode('listTarget'))->raw(
                ", \$context['list']);\n")->write("unset(\$else, \$context['else']);\n");
    }
}
