<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
$loader = require __DIR__ . '/../vendor/autoload.php';

$renderer = new \HuaForms2\Renderer('forms2/built/test.form.php');
$renderer->setValues(['name' => 'Huguet', 'gender' => 'M']);
$renderer->setCsrf('test_csrf', 'xxx');

$handler = new \HuaForms2\Handler('forms2/built/test.form.json');
$handler->setCsrf('test_csrf', 'xxx');
$ok = false;
$errors = [];
if ($handler->isSubmitted()) {
    if ($handler->isValid()) {
        $ok = true;
        $data = $handler->getFilteredData();
    } else {
        $errors = $handler->getErrorMessages();
        $renderer->setErrors($errors);
        $renderer->setValues($handler->getSelectiveData());
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
    <h1>Hello, world!</h1>

	<?php if ($ok): ?>
		<h2>Success</h2>
		<pre><?php var_dump($data); ?></pre>
	<?php else: ?>
	<?= $renderer->render(); ?>
	<?php endif; ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>