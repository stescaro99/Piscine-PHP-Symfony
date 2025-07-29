<?php
require_once __DIR__ . '/Elem.php';
require_once __DIR__ . '/TemplateEngine.php';

$root = new Elem('html');
$head = new Elem('head');
$title = new Elem('title', 'Shrek');
$head->pushElement($title);
$body = new Elem('body');
$body->pushElement(new Elem('h1', 'Shrek is love'));
$body->pushElement(new Elem('p', 'Shrek is life.'));
$root->pushElement($head);
$root->pushElement($body);
$engine = new TemplateEngine($root);
$engine->createFile('./output.html');
// test subject
$elem = new Elem('html');
$body = new Elem('body');
$body->pushElement(new Elem('p', 'Lorem ipsum'));
$elem->pushElement($body);
echo $elem->getHTML();
?>
