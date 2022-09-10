<?php

namespace App\Extensions\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonExtractFunction extends FunctionNode
{
    protected $jsonExpression;
    protected $keyJson;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'JSON_EXTRACT (' . $sqlWalker->walkArithmeticExpression( $this->jsonExpression ) . ',' . $sqlWalker->walkStringPrimary( $this->keyJson ) . ')';
    }

    public function parse(Parser $parser)
    {
        $parser->Match( Lexer::T_IDENTIFIER );
        $parser->Match( Lexer::T_OPEN_PARENTHESIS );

        $this->jsonExpression = $parser->ArithmeticExpression();
        $parser->Match( Lexer::T_COMMA );

        $this->keyJson = $parser->ArithmeticExpression();

        $parser->Match( Lexer::T_CLOSE_PARENTHESIS );
    }
}