# Object Oriented MySQLi Connection

Object oriented MySQLi connection with multiple methods of usage.

Although this code *should* function in earlier versions of PHP, it is recommended to use [supported PHP versions](https://secure.php.net/supported-versions.php).
**Unless with legitimate reason to continue using unsupported version of PHP, please upgrade as soon as possible.**

## Usage

### Basic query examples

```php
<?php
require_once 'db.php';
$db = new DB;

// with a variables and single value returned
$stmt = $db->mysqli->prepare("SELECT * FROM users WHERE username='$username'");
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
echo $user['username'];

// without variables and getting multiple rows
$result = $db->mysqli->query("SELECT * FROM users ORDER BY id ASC");
while ($row=$result->fetch_assoc()) {
    $users[] = $row;
}
$result->close();
print_r($users);
```

**Note:** If in a different folder, e.g. use `require_once 'class/db.php';` if *db.php* is in another folder named "class" within the same folder.

### Creating another class file called *user.php*

```php
<?php
class User extends DB {
    function signup($username, $password, $email) {
        //insert with getting insert id
        $stmt = $this->mysqli->prepare("INSERT INTO customers(username, password, email) VALUES ('$username', '$password', '$email')");
        $stmt->execute();
        return $stmt->insert_id;
        $stmt->close();
    }
```

#### In other PHP files:

```php
<?php
require_once 'db.php'
require_once 'user.php'
$inst = new User;
//Function call
$new_user = signup($username, $password, $email);
echo $new_user;
```

**Note:** Here, you have to include **both** files for this to work. This method is used to put other functions outside of the shared *db.php* class file.

### Using the provided `mysqli()` function

This is based off of the code in [Mysqli made simple](https://phpdelusions.net/mysqli/simple) at [phpdelusions.net](https://phpdelusions.net/).

```php
// with 2 variables and 1 row returned
$user = $this->mysqli("SELECT * FROM users WHERE username=? OR email=?", [$username, $email], "ss")->get_result()->fetch_row();
print_r($stmt);

// with 1 variable and single value returned
$user = $this->mysqli("SELECT * FROM users WHERE username=?", [$username], "s")->get_result()->fetch_assoc();
echo $user['username'];

// without variables and getting multiple rows
$result = $this->mysqli("SELECT * FROM users ORDER BY id ASC");
while ($row=$result->fetch_assoc()) {
    $users[] = $row;
}
print_r($users);

//insert with getting insert id
$stmt = $this->mysqli("INSERT INTO customers(username, password, email) VALUES (?, ?, ?)", [$username, $password, $email], "sss");
echo $stmt->insert_id;
```

**Notes:**

This function is meant to be used **only** with [prepared statements](https://secure.php.net/manual/en/mysqli.quickstart.prepared-statements.php). This is done in MySQLi using anonymous, positional placeholders with *?*.

Therefore, types for [bind_param()](https://secure.php.net/manual/en/mysqli-stmt.bind-param.php) have to be specified as shown at function call. If the third argument, `$types`, is not defined, this `mysqli()` function will default to the type **s** as in *string* for each variable which can break your query especially if any of the columns in the table specified in the query are of *int*, *float* or *double* data type.

The variables have to be in an **array** even if there is only 1 variable.

As of PHP 5.4 you can also use the short array syntax, which replaces
`array()` with `[]`.

##### Before PHP 5.4

```php
//insert with getting insert id
$stmt = $this->mysqli("INSERT INTO customers(username, password, email) VALUES (?, ?, ?)", array($username, $password, $email), "sss");
echo $stmt->insert_id;
```

### Backwards compatibility

There is an `if-else` statement which checks against version [5.6](https://secure.php.net/migration56.new-features) which introduces [argument unpacking using `...`](https://wiki.php.net/rfc/argument_unpacking) also known as the spread operator in JavaScript. The [`foreach`](https://secure.php.net/manual/en/control-structures.foreach.php) loop and [`call_user_func_array()`](https://secure.php.net/manual/en/function.call-user-func-array.php) used for older versions is referenced from [here at Stack Overflow](https://stackoverflow.com/questions/1913899/mysqli-binding-params-using-call-user-func-array).

There are also [array()](https://secure.php.net/manual/en/language.types.array.php#language.types.array.syntax.array-func) functions for compatibility with PHP before version [5.4](https://secure.php.net/migration54.new-features).



### One more thing:

There is a procedural version without using any class. This changes both making queries and using the `mysqli()` function.

### Basic query example but not class-based

Rename the *db-noclass.php* file to *db.php*.

#### Start

```php
<?php
require_once 'db.php';
```

#### Object oriented style

```php
// with a variables and single value returned
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username='$username'");
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
echo $user['username'];
```

#### Procedural style

```php
// with a variables and single value returned
$stmt = mysqli_prepare($mysqli, "SELECT * FROM users WHERE username='$username'");
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
echo $user['username'];
```

### Using the provided `mysqli()` function

This time is simpler.

```php
// with 2 variables and 1 row returned
$user = mysqli($mysqli, "SELECT * FROM users WHERE username=? OR email=?", [$username, $email], "ss")->get_result()->fetch_row();
print_r($stmt);

// with 1 variable and single value returned
$user = mysqli($mysqli, "SELECT * FROM users WHERE username=?", [$username], "s")->get_result()->fetch_assoc();
echo $user['username'];

// without variables and getting multiple rows
$result = mysqli($mysqli, "SELECT * FROM users ORDER BY id ASC");
while ($row=$result->fetch_assoc()) {
    $users[] = $row;
}
print_r($users);

//insert with getting insert id
$stmt = mysqli($mysqli, "INSERT INTO customers(username, password, email) VALUES (?, ?, ?)", [$username, $password, $email], "sss");
echo $stmt->insert_id;
```
