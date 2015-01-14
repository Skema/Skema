<?php
require_once ('vendor/autoload.php');
require_once ('default.config.php');

R::setup("mysql:host=$host;dbname=$db", $user, $password);

$var = new Skema\Set();