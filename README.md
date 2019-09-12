# pmql
ProcessMaker Query Language

Support for simple SQL-like expressions and converting to Laravel Eloquent.  Exposes a Eloquent scope 'pmql' to pass in clauses.

## Table of Contents
- [Simple Usage](#simple-usage)
- [Operators](#operators)
    - [Comparison Operators](#comparison-operators)
    - [Logical Operators](#logical-operators)
- [Case Sensitivity](#case-sensitivity)
- [Casting](#casting)
- [Dates](#dates)
- [Syntax Examples](#syntax-examples)
    - [Sample Dataset](#sample-dataset)
    - [Basic Syntax](#basic-syntax)
    - [And](#and)
    - [Or](#or)
    - [Grouping](#grouping)
    - [Numeric Comparison](#numeric-comparison)
    - [Casting To Number](#casting-to-number)
    - [Date Comparison](#date-comparison)
    - [Dynamic Date Comparison](#dynamic-date-comparison)
    - [Pattern Matching](#pattern-matching)
        - [Start of String](#start-of-string)
        - [Exact Pattern](#exact-pattern)
        - [End of String](#end-of-string)
        - [String Contains](#string-contains)
        - [Ignore Case](#ignore-case)
- [Custom Callbacks](#custom-callbacks)

## Simple Usage

```php
$results = Record::where('id', '<', 500)->pmql('username = "foobar" AND age < 25')->get();
```

## Operators

### Comparison Operators

| Operator | Name                     |
|----------|--------------------------|
| =        | Equal                    |
| !=       | Not Equal                |
| <        | Less Than                |
| >        | Greater Than             |
| <=       | Less Than or Equal To    |
| >=       | Greater Than or Equal To |
| LIKE     | Pattern Match            |

### Logical Operators

| Operator | Name                     |
|----------|--------------------------|
| AND      | Match both conditions    |
| OR       | Match either condition   |

## Case Sensitivity

Note that PMQL syntax is not case sensitive. However, queries are case sensitive. For example, if querying for a string, PMQL will return results only if the case matches your query exactly. This may be bypassed by utilizing the `lower(field)` syntax. Examples are provided below.

## Casting

Fields can be cast to various data types using the `cast(field as type)` syntax. Currently supported types are _text_ and _number_. Examples are provided below.

## Dates

Strings entered in the format `"YYYY-MM-DD"` are interpreted as dates and can be used in comparative queries. Dates can be compared dynamically based on the current time utilizing the `now` keyword. Arithmetic operations can be performed on dates using the `date (+ or -)number interval` syntax. The interval can be either `day`, `hour`, `minute`, or `second`. Examples are provided below.


## Syntax Examples

### Sample Dataset

Let's say we are managing a roster for a basketball team.

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 8  | Liz        | Cambage   | center   | 1991-08-18 | 3          | true    |
| 51 | Sydney     | Colson    | guard    | 1989-08-06 | 5          | false   |
| 5  | Dearica    | Hamby     | forward  | 1993-11-06 | 4          | false   |
| 21 | Kayla      | McBride   | guard    | 1992-06-25 | 5          | true    |
| 19 | JiSu       | Park      | center   | 1998-12-06 | 1          | false   |
| 10 | Kelsey     | Plum      | guard    | 1994-08-24 | 2          | true    |
| 11 | Epiphanny  | Prince    | guard    | 1988-01-11 | 9          | false   |
| 14 | Sugar      | Rodgers   | guard    | 1989-12-08 | 6          | false   |
| 4  | Carolyn    | Swords    | center   | 1989-07-19 | 7          | false   |
| 22 | A'ja       | Wilson    | forward  | 1996-08-08 | 1          | true    |
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |
| 0  | Jackie     | Young     | guard    | 1997-09-16 | 0          | true    |

---

### Basic Syntax

Find players with a specific last name.

#### Query

```sql
last_name = "Young"
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |
| 0  | Jackie     | Young     | guard    | 1997-09-16 | 0          | true    |

---

### And

Find players with a specific last name in a specific position.

#### Query

```sql
last_name = "Young" and position = "forward"
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |

---

### Or

Find players in two different positions.

#### Query

```sql
position = "center" or position = "forward"
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 8  | Liz        | Cambage   | center   | 1991-08-18 | 3          | true    |
| 5  | Dearica    | Hamby     | forward  | 1993-11-06 | 4          | false   |
| 19 | JiSu       | Park      | center   | 1998-12-06 | 1          | false   |
| 4  | Carolyn    | Swords    | center   | 1989-07-19 | 7          | false   |
| 22 | A'ja       | Wilson    | forward  | 1996-08-08 | 1          | true    |
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |

---

### Grouping

Find players matching grouped criteria:

#### Query

```sql
(position = "center" or position = "forward") and starter = "true"
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 8  | Liz        | Cambage   | center   | 1991-08-18 | 3          | true    |
| 22 | A'ja       | Wilson    | forward  | 1996-08-08 | 1          | true    |

---

### Numeric Comparison

Find players based on years of experience.

#### Query

```sql
experience > 8
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 11 | Epiphanny  | Prince    | guard    | 1988-01-11 | 9          | false   |
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |

---

### Casting To Number

What if a field we want to compare mathematically is stored as a string instead of an integer? No problem. We can simply cast it as a number.

Let's say our dataset has changed to store the _experience_ field as a string but we want to find all players with 2 years of experience or less.

#### Query

```sql
cast(experience as number) <= 2
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 19 | JiSu       | Park      | center   | 1998-12-06 | 1          | false   |
| 10 | Kelsey     | Plum      | guard    | 1994-08-24 | 2          | true    |
| 22 | A'ja       | Wilson    | forward  | 1996-08-08 | 1          | true    |
| 0  | Jackie     | Young     | guard    | 1997-09-16 | 0          | true    |

---

### Date Comparison

Find players born before 1990.

#### Query

```sql
dob < "1990-01-01"
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 51 | Sydney     | Colson    | guard    | 1989-08-06 | 5          | false   |
| 11 | Epiphanny  | Prince    | guard    | 1988-01-11 | 9          | false   |
| 14 | Sugar      | Rodgers   | guard    | 1989-12-08 | 6          | false   |
| 4  | Carolyn    | Swords    | center   | 1989-07-19 | 7          | false   |
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |

---

### Dynamic Date Comparison

Find players under 25 as of right now. We utilize the `now` keyword and subtract 9,125 days (365 * 25 = 9,125).

#### Query

```sql
dob > now -9125 day
```

#### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 19 | JiSu       | Park      | center   | 1998-12-06 | 1          | false   |
| 22 | A'ja       | Wilson    | forward  | 1996-08-08 | 1          | true    |
| 0  | Jackie     | Young     | guard    | 1997-09-16 | 0          | true    |

---

### Pattern Matching

We can use the `LIKE` operator to perform pattern matching with a field. `%` is a wildcard which matches zero, one, or more characters. `_` is a wildcard which matches one character.

#### Start of String

Let's find all players whose last names begin with the letter P.

##### Query

```sql
last_name like "P%"
```

##### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 19 | JiSu       | Park      | center   | 1998-12-06 | 1          | false   |
| 10 | Kelsey     | Plum      | guard    | 1994-08-24 | 2          | true    |
| 11 | Epiphanny  | Prince    | guard    | 1988-01-11 | 9          | false   |

#### Exact Pattern

Let's find all players whose last names begin with P and have three letters after that.

##### Query

```sql
last_name like "P___"
```

##### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 19 | JiSu       | Park      | center   | 1998-12-06 | 1          | false   |
| 10 | Kelsey     | Plum      | guard    | 1994-08-24 | 2          | true    |

#### End of String

Let's find all players whose last names end in "son."

##### Query

```sql
last_name like "%son"
```

##### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 51 | Sydney     | Colson    | guard    | 1989-08-06 | 5          | false   |
| 22 | A'ja       | Wilson    | forward  | 1996-08-08 | 1          | true    |

#### String Contains

Let's find all players whose names contain "am."

##### Query

```sql
first_name like "%am%" or last_name like "%am%"
```

##### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 8  | Liz        | Cambage   | center   | 1991-08-18 | 3          | true    |
| 5  | Dearica    | Hamby     | forward  | 1993-11-06 | 4          | false   |
| 1  | Tamera     | Young     | forward  | 1986-10-30 | 11         | false   |

#### Ignore Case

Let's find all players whose names contain "de" regardless of capitalization.

##### Query

```sql
lower(first_name) like "%de%" or lower(last_name) like "%de%"
```

##### Result

| id | first_name | last_name | position | dob        | experience | starter |
|----|------------|-----------|----------|------------|------------|---------|
| 5  | Dearica    | Hamby     | forward  | 1993-11-06 | 4          | false   |
| 21 | Kayla      | McBride   | guard    | 1992-06-25 | 5          | true    |

---

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