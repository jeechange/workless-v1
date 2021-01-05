<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Driver;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * Description of RegexpOci8
 *
 * @author Administrator
 */
class RegexpOci8 extends FunctionNode {

    public $firstRegExExpression = null;
    public $secondRegExExpression = null;

    /**
     * Parse the query expression
     *
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstRegExExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondRegExExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Return the created string representation
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
        return 'REGEXP_LIKE(' . $this->firstRegExExpression->dispatch($sqlWalker) . ',' . $this->secondRegExExpression->dispatch($sqlWalker) . ')AND 1';
    }

}
