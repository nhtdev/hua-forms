<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

use HuaForms\FormFactory;
use HuaForms\Renderer;

FormFactory::global()->addPath(__DIR__ . '/forms/');

$form = FormFactory::global()->get('simple.form.html');
$renderer = new Renderer();
echo $renderer->render($form);
