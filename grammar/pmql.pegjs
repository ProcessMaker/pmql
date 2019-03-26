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
  // Header/utility functions for sql.pegjs grammar match bodies.
  //
  function append($arr, $x) {
    $arr[] = $x;
    return $arr;
  }

  function isAssoc(array $arr)
  {
      if (array() === $arr) return false;
      return array_keys($arr) !== range(0, count($arr) - 1);
  }


  function flatten($x, $rejectSpace, $acc = []) {
    if ($x == null) {
      if (!$rejectSpace) {
        return append($acc, $x);
      }
      return $acc;
    }
    if (isAssoc($x)) { 
      return append($acc, $x);
    }
    if (rejectSpace &&
      ((strlen($x) == 0) ||
       ( is_string(x) && preg_match('/^\s*$/', $x)))) {
      return acc;
    }
    if (is_string(x)) {
      return append(acc, x);
    }
    // Handle array
    for ($i = 0; $i < count($x); $i++) {
      flatten($x[$i], $rejectSpace, $acc);
    }
    return $acc;
  }

  function flatstr($x, $rejectSpace, $joinChar = '') {
    return implode($joinChar, flatten($x, $rejectSpace, []));
  }

}

start = ex:expr { return flatten(ex, true); }

expr =
  e: ( whitespace
       ( ( value binary_operator expr )
       / ( value NOT ? ( LIKE / GLOB / REGEXP / MATCH ) expr ( ESCAPE expr )? )
       / ( value ( ISNULL / NOTNULL / ( NOT NULL ) ) )
       / ( value IS NOT ? expr )
       / ( value NOT ? BETWEEN expr AND expr )
       / value ) )
  { return flatten(e[1]); }

type_name =
  ( name )+
  ( ( lparen signed_number rparen )
  / ( lparen signed_number comma signed_number rparen ) )?

signed_number =
  ( ( plus / minus )? numeric_literal )

value =
  v: ( whitespace
         ( ( j: json_data_name
           { return [ 'json_data_name' => $j ]; } )
       / ( x: literal_value
           { return [ 'literal' => x ]; } )
       / ( c: column_name
           { return [ 'column' => c ]; } )

       / ( unary_operator expr )
       / call_function
       / ( whitespace lparen expr whitespace rparen )
       / ( CAST lparen expr AS type_name rparen ) ) )
  { return v[1]; }


call_function =
  ( function_name
    whitespace lparen
               ( ( DISTINCT ? ( expr (whitespace comma expr)* )+ )
               / whitespace star )?
    whitespace rparen )

json_data_name = "data" dot el:json_element { return flatstr(el); }

json_element =  el:((json_array_element / name) (dot json_element)*) { return flatten(el); }

json_array_element = name lbrack digit+ rbrack

literal_value =
  ( numeric_literal / string_literal 
  / NULL / CURRENT_TIME / CURRENT_DATE / CURRENT_TIMESTAMP )

numeric_literal =
  digits:( ( ( ( digit )+ ( decimal_point ( digit )+ )? )
           / ( decimal_point ( digit )+ ) )
           ( E ( plus / minus )? ( digit )+ )? )
  { $x = flatstr($digits);
    if (strpos(x, '.') >= 0) {
      return floatval(x);
    }
    return intval(x);
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
string_literal = str:('"' (escape_char / [^"])* '"') { return implode("", flatten(str)); }
escape_char = '\\' .
nil = ''

whitespace =
  [ \t\n\r]*
whitespace1 =
  [ \t\n\r]+

unary_operator =
  x: ( whitespace
       ( '-' / '+' / '~' / 'NOT'i) )
  { return x[1]; }

binary_operator =
  x: ( whitespace
       ('||'
        / '*' / '/' / '%'
        / '+' / '-'
        / '<<' / '>>' / '&' / '|'
        / '<=' / '>='
        / '<' / '>'
        / '=' / '==' / '!=' / '<>'
        / 'IS'i / 'IS NOT'i / 'IN'i / 'LIKE'i / 'GLOB'i / 'MATCH'i / 'REGEXP'i
        / 'AND'i
        / 'OR'i) )
  { return x[1]; }

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