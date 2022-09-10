<?php

namespace App\Extensions\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class RandFunction extends FunctionNode
{

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'RAND()';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

//How Use
//$q = $this->createQueryBuilder('e')
//    ->addSelect('RAND() as HIDDEN rand')
//    ->orderBy('rand');