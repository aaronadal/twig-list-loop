<?php

namespace Aaronadal\TwigListLoop\Twig;


use Twig\Node\Expression\AssignNameExpression;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Creates a list iterating over an array.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class ListTokenParser extends AbstractTokenParser
{

    const TAG = 'list';

    /**
     * {@inheritdoc}
     */
    public function parse(Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        // Parse for loop
        $targets = $parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(Token::OPERATOR_TYPE, 'in');
        $sequence = $parser->getExpressionParser()->parseExpression();

        // Parse if expression
        $if = null;
        if($stream->nextIf(Token::NAME_TYPE, 'if')) {
            $if = $parser->getExpressionParser()->parseExpression();
        }

        // Parse listing template
        $stream->expect(Token::NAME_TYPE, 'using');
        $template = $this->parser->getExpressionParser()->parseExpression();

        // Parse additional args
        $args = null;
        if($stream->nextIf(Token::NAME_TYPE, 'with')) {
            $args = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        // Subparse body
        $body = $parser->subparse(array($this, 'decideListBodyEnd'));

        // Subparse else
        $else = null;
        if($stream->next()->getValue() == 'else') {
            $stream->expect(Token::BLOCK_END_TYPE);
            $else = $parser->subparse(array($this, 'decideListElseEnd'), true);
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        // Parse for loop variable names
        if(count($targets) > 1) {
            $keyTarget   = $targets->getNode(0);
            $keyTarget   = new AssignNameExpression(
                $keyTarget->getAttribute('name'),
                $keyTarget->getTemplateLine()
            );

            $valueTarget = $targets->getNode(1);
            $valueTarget = new AssignNameExpression(
                $valueTarget->getAttribute('name'),
                $valueTarget->getTemplateLine()
            );
        }
        else {
            $keyTarget   = new AssignNameExpression('_key', $token->getLine());
            $valueTarget = $targets->getNode(0);
            $valueTarget = new AssignNameExpression(
                $valueTarget->getAttribute('name'),
                $valueTarget->getTemplateLine()
            );
        }

        // Create loop node
        $listTarget = new AssignNameExpression('list', $token->getLine());
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

    public function decideListBodyEnd(Token $token)
    {
        return $token->test(array('else', 'endlist'));
    }

    public function decideListElseEnd(Token $token)
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
