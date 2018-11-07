<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
<style>
input[type="radio"]:not(:checked) ~ .hidden {display: none;}
input[type="radio"]:checked ~ .hidden {display: inherit;}
</style>
</head>
<body>
<p>The <a href="https://php.net/manual/en/function.password-hash.php">password_hash()</a> function in PHP</p>
<?php echo '<p>PHP '.phpversion().'</p>'; ?>
<?php if (version_compare(PHP_VERSION, '5.5.0') >= 0) { ?>
<?php if (constant("PASSWORD_ARGON2I") == 2) { ?>
<form action="" method="post" name="form">
    <span>Select password encryption</span><br>
    <input type="radio" name="algo" value="argon"> Argon2<br>
    <input type="radio" name="algo" value="bcrypt"> bcrypt<br><br>
    <div class="hidden">
        <span>Enter text to encrypt</span><br>
        <input type="text" name="input" autocomplete="off">
        <input type="submit" name="submit" value="enter">
    </div>
</form>
<?php
$hash='';
if (!empty($_POST['submit'])) {
    $algo=$_POST['algo'];
    $text=$_POST['input'];
    if ($algo == argon) {
        $hash = password_hash($text, PASSWORD_ARGON2I);
    } elseif ($algo == bcrypt) $hash = password_hash($text, PASSWORD_BCRYPT);
    echo '<pre style="font-size: 1.2em;">'.$hash.'</pre>';
}
?>
<?php } else { ?>
<form action="" method="post" name="form">
    <p>(Only bcrypt is available for PHP 5.5.0 to 7.2.0 and XAMPP builds.)</p>
    <span>Enter text to encrypt</span><br>
    <input type="text" name="input" autocomplete="off">
    <input type="submit" name="submit" value="enter">
</form>
<?php
$hash='';
if (!empty($_POST['submit'])) {
    $text=$_POST['input'];
    $hash = password_hash($text, PASSWORD_DEFAULT);
    echo '<pre style="font-size: 1.2em;">'.$hash.'</pre>';
}
?>
<?php } ?>
<?php } else echo '<p>PHP below 5.5.0 cannot use the password_hash function.</p>' ?>
</body>
</html>
