<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig_Node_Expression_AssignName;
use Twig_Token;

/**
 * Creates a list iterating over an array.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListTokenParser extends \Twig_TokenParser
{

    const TAG = 'list';

    /**
     * {@inheritdoc}
     */
    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        // Parse for loop
        $targets = $parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(Twig_Token::OPERATOR_TYPE, 'in');
        $sequence = $parser->getExpressionParser()->parseExpression();

        // Parse if expression
        $if = null;
        if($stream->nextIf(Twig_Token::NAME_TYPE, 'if')) {
            $if = $parser->getExpressionParser()->parseExpression();
        }

        // Parse listing template
        $stream->expect(Twig_Token::NAME_TYPE, 'using');
        $template = $this->parser->getExpressionParser()->parseExpression();

        // Parse additional args
        $args = null;
        if($stream->nextIf(Twig_Token::NAME_TYPE, 'with')) {
            $args = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        // Subparse body
        $body = $parser->subparse(array($this, 'decideListBodyEnd'));

        // Subparse else
        $else = null;
        if($stream->next()->getValue() == 'else') {
            $stream->expect(Twig_Token::BLOCK_END_TYPE);
            $else = $parser->subparse(array($this, 'decideListElseEnd'), true);
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        // Parse for loop variable names
        if(count($targets) > 1) {
            $keyTarget   = $targets->getNode(0);
            $keyTarget   = new Twig_Node_Expression_AssignName(
                $keyTarget->getAttribute('name'),
                $keyTarget->getTemplateLine()
            );

            $valueTarget = $targets->getNode(1);
            $valueTarget = new Twig_Node_Expression_AssignName(
                $valueTarget->getAttribute('name'),
                $valueTarget->getTemplateLine()
            );
        }
        else {
            $keyTarget   = new Twig_Node_Expression_AssignName('_key', $token->getLine());
            $valueTarget = $targets->getNode(0);
            $valueTarget = new Twig_Node_Expression_AssignName(
                $valueTarget->getAttribute('name'),
                $valueTarget->getTemplateLine()
            );
        }

        // Create loop node
        $listTarget = new Twig_Node_Expression_AssignName('list', $token->getLine());
        $listLoop = $this->createForLoop($keyTarget, $valueTarget, $listTarget, $sequence, $if, $body, $token->getLine(), self::TAG);

        // Return list node
        return new ListNode($template, $listTarget, $listLoop, $args, $else, $token->getLine(), self::TAG);
    }

    private function createForLoop($keyTarget, $valueTarget, $listTarget, $sequence, $if, $body, $lineno, $tag)
    {
        $loopBody = new ListLoopBodyNode($keyTarget, $valueTarget, $listTarget, $body, $lineno, $tag);
        $forLoop = new ListLoopNode($keyTarget, $valueTarget, $sequence, $if, $loopBody, $lineno, $tag);

        return $forLoop;
    }

    public function decideListBodyEnd(Twig_Token $token)
    {
        return $token->test(array('else', 'endlist'));
    }

    public function decideListElseEnd(Twig_Token $token)
    {
        return $token->test('endlist');
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return self::TAG;
    }
}
