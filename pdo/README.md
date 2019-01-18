# Object Oriented PDO Connection

Object oriented PDO connection with at least 4 ways of performing queries.

## Introduction

The connection is made using `new pdo()`. Check out this guide on [making a database connection](https://phpdelusions.net/pdo#dsn).

No need to use `die()` or `try-catch` in the code. The mode for error handling is set using `PDO::ATTR_ERRMODE` while `ini_set('display_errors', 1);` enables errors to be displayed. For more explanation see this guide on [error handling in **PDO**](https://phpdelusions.net/pdo#errors).

**PDO** ships with PHP 5.1, and is available as a *PECL* extension for PHP 5.0.

**PDO requires the new OO features in the core of PHP 5, and so will not run with earlier versions of PHP.**

Even so, [supported PHP versions](https://secure.php.net/supported-versions.php) have since moved beyond PHP 5.1 so it is recommended to upgrade as soon as possible.

## Usage

### Basic query examples

```php
<?php
require_once 'db.php';
$db = new DB;

// using a named parameter and showing single value
$stmt = $db->pdo->prepare("SELECT * FROM users WHERE name=:name");
$stmt->execute([ 'name' => $name ]);
$user = $stmt->fetch();
$stmt = null;
echo $user['name'];

// using a positional parameter and showing single value
$stmt = $db->pdo->prepare("SELECT * FROM users WHERE name=?");
$stmt->execute([$name]);
$user = $stmt->fetch();
$stmt = null;
echo $user['name'];

// without variables and getting multiple rows
$result = $db->pdo->query("SELECT * FROM users ORDER BY id ASC");
while ($row=$result->fetch()) {
    $users[] = $row;
}
$result = null;
print_r($users);

//insert using named parameters with getting insert id
$stmt = $this->pdo->prepare("INSERT INTO users(name, contact, email) VALUES (:name, :contact, :email)");
$array = [
    'email' => $email,
    'name' => $name,
    'contact' => $contact
];
$stmt->execute($array);
echo $stmt->lastInsertId;
$stmt = null;

//insert using positional parameters with getting insert id
$stmt = $this->pdo->prepare("INSERT INTO users(name, contact, email) VALUES (?, ?, ?)");
$array = [$name, $contact, $email];
$stmt->execute($array);
echo $stmt->lastInsertId;
$stmt = null;
```

> **Note:**
>
> If in a different folder, e.g. use `require_once 'class/db.php';` if **db.php** is in another folder named "**class**" within the same folder.
>
> The examples above use [prepared statements](https://phpdelusions.net/pdo#prepared) so even with just **1** variable, an **array** is still needed on execution. **PDO** can also be used with the usual single quotes.
>
> Using *named parameters* require an **associative array** whereas *positional parameters* simply require an **indexed/numeric array**.
>
> Using *named parameters*, the elements in the **associative array** can be of **any order**.
>
> Using *positional parameters* require the elements in the **indexed/numeric array** to be in the **same order** as the order of the columns defined in the query.
>
> Unlike **MySQLi**, **PDO** does not use a `close()` function. Instead, variables are set to null.
>
> Also unlike **MySQLi**, **PDO** it has a default fetch mode on connection, which is the *fetch associative array* mode in this case. It also has different functions for fetching data. Refer to [this guide on fetch modes](https://phpdelusions.net/pdo/fetch_modes) and [The PDOStatement class of the PHP documentation](https://secure.php.net/manual/en/class.pdostatement.php) for more information.

### Creating another class file called "*user.php*"

```php
<?php
class User extends DB {
    function signup($name, $contact, $email) {
        //insert with getting insert id
        $stmt = $this->pdo->prepare("INSERT INTO users(name, contact, email) VALUES (?, ?, ?)");
        $stmt->execute();
        return $stmt->insert_id;
        $stmt->close();
    }
```

> **Note:** Other than `$this->pdo`, using `$this->connect()` also works since **protected** functions and variables can be used inside other functions whether in the same class or extended. Refer to [this documentation](https://secure.php.net/manual/en/language.oop5.visibility.php) for more information.

#### In other PHP files:

```php
<?php
require_once 'db.php'
require_once 'user.php'
$inst = new User;
//Function call
$new_user = signup($name, $contact, $email);
echo $new_user;
```

> **Note:** Here, you have to include **both** files for this to work. This method is used to put other functions outside of the shared **db.php** class file.

### Using the provided `pdo()` function

```php
<?php
require_once 'db.php';
$db = new DB;

// using a named parameter and showing single value
$user = $db->pdo("SELECT * FROM users WHERE name=:name", [ 'name' => $name ])->fetch();
echo $user['name'];

// with named parameters with binding and 1 row returned
$user = $db->pdo("SELECT * FROM users WHERE name=? OR email=?", [$name, $email], true)->fetch();
print_r($user);
$user = null;

// using a positional parameter and showing single value
$user = $db->pdo("SELECT * FROM users WHERE name=?", [$name])->fetch();
echo $user['name'];

// without variables and getting multiple rows
$result = $db->pdo("SELECT * FROM users ORDER BY id ASC");
while ($row=$result->fetch()) {
    $users[] = $row;
}
$result = null;
print_r($users);

//insert using named parameters with getting insert id
$array = [
    'email' => $email,
    'name' => $name,
    'contact' => $contact
];
$stmt = $this->pdo("INSERT INTO users(name, contact, email) VALUES (:name, :contact, :email)", $array);
echo $stmt->lastInsertId;
$stmt = null;

//insert using positional parameters with getting insert id
$stmt = $this->pdo("INSERT INTO users(name, contact, email) VALUES (?, ?, ?)", [$name, $contact, $email]);
echo $stmt->lastInsertId;
$stmt = null;

//insert using positional parameters with binding and getting insert id
$stmt = $this->pdo("INSERT INTO users(name, contact, email) VALUES (?, ?, ?)", [$name, $contact, $email], [PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_STR]);
echo $stmt->lastInsertId;
$stmt = null;
```

> **Note:**
>
> Unlike **MySQLi**, binding parameters is optional since **PDO** can already execute arrays.
>
> If you choose to bind parameters, you can add a `$types` array as the third argument of this `pdo()` function with [**PDO::PARAM_*** constants](https://php.net/manual/en/pdo.constants.php) as its values.
>
> Both arrays have to be of the **same number of values**, *i.e.* same exact `count()`. The position of each value, *i.e.* `strpos()`, in the `$types` array corresponds to the position of each value, *i.e.* `array_search()`, in the `$params` array.
>
> If you simply define the third argument of this `pdo()` function instead as `true` (without quotes), then each parameter will be bound as string which will only work if **all** of the parameters in the query are of the *string* data type such as **char**, **varchar** or **text**.
>
> As of PHP 5.4 you can also use the short array syntax, which replaces `array()` with `[]`.

##### Before PHP 5.4

```php
//insert using positional parameters with getting insert id
$stmt = $this->pdo("INSERT INTO customers(name, contact, email) VALUES (?, ?, ?)", array($name, $contact, $email);
echo $stmt->lastInsertId;
$stmt = null;
```

### Backwards compatibility

There is an `if-else` statement which checks against version [5.6](https://secure.php.net/migration56.new-features) which introduces [argument unpacking using `...`](https://wiki.php.net/rfc/argument_unpacking) also known as the spread operator in JavaScript. This is used in the `bind_param()` statement as explained by [this person at Stack Overflow](https://stackoverflow.com/a/40718151), who is the site owner of *phpdelusions.net*.

The [`foreach`](https://secure.php.net/manual/en/control-structures.foreach.php) loop and [`call_user_func_array()`](https://secure.php.net/manual/en/function.call-user-func-array.php) used for older versions is referenced from [here at Stack Overflow](https://stackoverflow.com/a/35542447).

There are also [`array()`](https://secure.php.net/manual/en/language.types.array.php#language.types.array.syntax.array-func) functions for compatibility with PHP before version [5.4](https://secure.php.net/migration54.new-features).



## One more thing . . .

There is a procedural version without using classes. This changes both making queries and using the `pdo()` function.

### Basic query example without using classes

Rename the **db-noclass.php** file to **db.php**.

```php
<?php
require_once 'db.php';
// with a variables and showing single value
$stmt = $pdo->prepare("SELECT * FROM users WHERE name='$name'");
$stmt->execute();
$user = $stmt->fetch();
$stmt = null;
echo $user['name'];
```

### Using the provided `pdo()` function

This is without using a class.

```php
<?php
require_once 'db.php';

// using a named parameter and showing single value
$stmt = pdo($pdo, "SELECT * FROM users WHERE name=:name", [ 'name' => $name ]);
$user = $stmt->fetch();
$stmt = null;
echo $user['name'];

// with named parameters with binding and 1 row returned
$stmt = pdo($pdo, "SELECT * FROM users WHERE name=? OR email=?", [$name, $email], true);
$user = $stmt->fetch();
$stmt = null;
print_r($user);

// using a positional parameter and showing single value
$stmt = pdo($pdo, "SELECT * FROM users WHERE name=?", [$name]);
$user = $stmt->fetch();
$stmt = null;
echo $user['name'];

// without variables and getting multiple rows
$result = pdo($pdo, "SELECT * FROM users ORDER BY id ASC");
while ($row=$result->fetch()) {
    $users[] = $row;
}
$result = null;
print_r($users);

//insert using named parameters with getting insert id
$array = [
    'email' => $email,
    'name' => $name,
    'contact' => $contact
];
$stmt = pdo($pdo, "INSERT INTO users(name, contact, email) VALUES (:name, :contact, :email)", $array);
echo $stmt->lastInsertId;
$stmt = null;

//insert using positional parameters with getting insert id
$stmt = pdo($pdo, "INSERT INTO users(name, contact, email) VALUES (?, ?, ?)", [$name, $contact, $email]);
echo $stmt->lastInsertId;
$stmt = null;

//insert using positional parameters with binding and getting insert id
$stmt = pdo($pdo, "INSERT INTO users(name, contact, email) VALUES (?, ?, ?)", [$name, $contact, $email], [PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_STR]);
echo $stmt->lastInsertId;
$stmt = null;
```
