<?php
include('./Coffee.php');
include('./Tea.php');
include('./TemplateEngine.php');

$Tea = new Tea();
$Coffee = new Coffee();
$templateEngine = new TemplateEngine();
$hotChocolate = new HotBeverage("Hot Chocolate", 3.00, 3);
$templateEngine->createFile($Tea);
$templateEngine->createFile($Coffee);
$templateEngine->createFile($hotChocolate);
unset($Tea);
unset($Coffee);
unset($hotChocolate);
unset($templateEngine);
?>
