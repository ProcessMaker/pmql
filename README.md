# pmql
ProcessMaker Query Language

Support for simple SQL-like expressions and converting to Laravel Eloquent.  Exposes a Eloquent scope 'pmql' to pass in clauses.

## Simple Usage

```php
$results = Record::where('id', '<', 500)->pmql('username = "foobar" AND age < 25')->get();
```

## Custom Callbacks
You can utilize custom callbacks in your pmql call to override behavior for a specific expression

```php
$results = Record::where('id', '<', 500)->pmql('username = "FOOBAR" AND age < 25', function($expression) {
    // This example will ensure checking for lowercase usernames as thats how it stored in our database
    if($expression->field->field() == 'username') {
        // If you want to modify the query, you need to return an anonymous function that will add your additional criteria
        return function($query) use($expression) {
                $query->where(DB::raw('LOWER(username)', $expression->operator, strtolower($expression->value->value()));
        }
    }
    // Let default behavior win for non username fields
    return false;
})->get();
```