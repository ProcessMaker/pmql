/**
* This ProcessMaker Query Language Grammar is based off of a subset 
* of SQL. Column names and values are validated by a callback passed in through 
* the options variable or is passed-thru. A laravel eloquent query object is 
* also passed through as the starting point.
* The query language only provides the where clause of a SQL statement.
* The ordering and limiting is meant to be handled by the PMQL caller.
*
* Things not supported:
*  * Explicit joins
*  * Select of specific columns
*  * Order by and limit clauses
*
*/

{
  // Any code that needs to be added to the language parser can go here

}

start = expr

expr =
  e: ( whitespace ( value binary_operator value ) ) { return $e[1]; }

type_name =
  ( name )+
  ( ( lparen signed_number rparen )
  / ( lparen signed_number comma signed_number rparen ) )?

signed_number =
  ( ( plus / minus )? numeric_literal )

value =
  v: ( whitespace
         ( ( j: json_data_name
           { return [ 'type' => 'json_field', 'value' => $j ]; } )
       / ( x: literal_value
           { return [ 'type' => 'literal', 'value' => $x ]; } )
       / ( c: column_name
           { return [ 'type' => 'field', 'value' => $c ]; } )

       / ( unary_operator expr )
       / call_function
       / subexpr:( whitespace lparen expr whitespace rparen ) { return $subexpr[2]; } // Only return the sub expression array, we don't need parenthesis in the final parse tree
       / ( CAST lparen expr AS type_name rparen ) ) )
  { return $v[1]; }


call_function =
  ( function_name
    whitespace lparen
               ( ( DISTINCT ? ( expr (whitespace comma expr)* )+ )
               / whitespace star )?
    whitespace rparen )

json_data_name = dn:("data" dot json_element) { return \ProcessMaker\Query\Processor::flatstr($dn, true); }

json_element =  el:((json_array_element / name) (dot json_element)*) { return \ProcessMaker\Query\Processor::flatstr($el, true); }

json_array_element = ae:(name lbrack digit+ rbrack) { return \ProcessMaker\Query\Processor::flatstr($ae); }

literal_value =
  ( numeric_literal / string_literal )

numeric_literal =
  digits:( ( ( ( digit )+ ( decimal_point ( digit )+ )? )
           / ( decimal_point ( digit )+ ) )
           ( E ( plus / minus )? ( digit )+ )? )
  { $x = \ProcessMaker\Query\Processor::flatstr($digits);
    // If there's a decimal point, then absolutely return float val
    if (strpos($x, '.') !== false) {
      return floatval($x);
    }
    // Otherwise, return the integer value
    return intval($x);
  }

/** Helper definitions **/
dot = '.'
comma = ','
minus = '-'
plus = '+'
lparen = '('
rparen = ')'
lbrack = '['
rbrack = ']'
star = '*'
newline = '\n'
string_literal = str:('"' (escape_char / [^"])* '"') { return \ProcessMaker\Query\Processor::flatstr($str[1]); }
escape_char = '\\' .
nil = ''

whitespace =
  [ \t\n\r]*
whitespace1 =
  [ \t\n\r]+

unary_operator =
  x: ( whitespace
       ( '-' / '+' / '~' / 'NOT'i) )
  { return $x[1]; }

binary_operator =
  x: ( whitespace
        ( '<=' / '>='
        / '<' / '>'
        / '=' / '==' / '!=' / '<>'
        / 'AND'i
        / 'OR'i) )
  { return ['type' => 'operator', 'value' => strtoupper($x[1]) ]; }

digit = [0-9]
decimal_point = dot
equal = '='

name =
  str:[A-Za-z0-9_]+
  { return implode('', $str); }

column_name = name
function_name = name


CURRENT_TIME = 'now'
CURRENT_DATE = 'now'
CURRENT_TIMESTAMP = 'now'

end_of_input = ''

/** Keyword definitions */
AND = whitespace1 "AND"i
AS = whitespace1 "AS"i
BETWEEN = whitespace1 "BETWEEN"i
CAST = whitespace1 "CAST"i
DISTINCT = whitespace1 "DISTINCT"i
E =
  "E"i
ESCAPE = whitespace1 "ESCAPE"i
GLOB = whitespace1 "GLOB"i
IS = whitespace1 "IS"i
ISNULL = whitespace1 "ISNULL"i
LIKE = whitespace1 "LIKE"i
MATCH = whitespace1 "MATCH"i
NOT = whitespace1 "NOT"i
NOTNULL = whitespace1 "NOTNULL"i
NULL = whitespace1 "NULL"i
OR = whitespace1 "OR"i
REGEXP = whitespace1 "REGEXP"i