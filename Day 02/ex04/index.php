<?php
require_once __DIR__ . '/Elem.php';
require_once __DIR__ . '/TemplateEngine.php';

$root = new Elem('html', '', ['lang' => 'en']);
$head = new Elem('head');
$title = new Elem('title', 'Shrek', ['id' => 'main-title']);
$head->pushElement($title);
$body = new Elem('body', '', ['class' => 'container']);
$body->pushElement(new Elem('h1', 'Shrek is love', ['class' => 'header']));
$body->pushElement(new Elem('p', 'Shrek is life.', ['class' => 'intro']));
$table = new Elem('table');
$trHead = new Elem('tr');
$trHead->pushElement(new Elem('th', 'Shrek'));
$trHead->pushElement(new Elem('th', 'Ciuchino'));  
$table->pushElement($trHead);
$trRow = new Elem('tr');
$td1 = new Elem('td');
$ul = new Elem('ul');
$ul->pushElement(new Elem('li', 'come le cipolle'));
$ul->pushElement(new Elem('li', 'meglio fuori che dentro'));
$ul->pushElement(new Elem('li', 'santo piripillo'));
$td1->pushElement($ul);
$td2 = new Elem('td');
$ol = new Elem('ol');
$ol->pushElement(new Elem('li', "Non ha le dita"));
$ol->pushElement(new Elem('li', "Sua moglie e' una draghessa"));
$ol->pushElement(new Elem('li', "E' un'ottusa, irritante, bestia da soma in miniatura"));
$td2->pushElement($ol);
$trRow->pushElement($td1);
$trRow->pushElement($td2);
$table->pushElement($trRow);
$body->pushElement($table);
$root->pushElement($head);
$root->pushElement($body);
$engine = new TemplateEngine($root);
$engine->createFile('./output.html');

// test subject
$elem = new Elem('html');
$body = new Elem('body');
$body->pushElement(new Elem('p', 'Lorem ipsum', ['class'=> 'text-muted']));
$elem->pushElement($body);
echo $elem->getHTML();
try
{
    $elem = new Elem('undefined');
}
catch (MyException $e) 
{
    echo $e->getMessage() . "\n";
}
?>
