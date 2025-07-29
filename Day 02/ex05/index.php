<?php
require_once __DIR__ . '/Elem.php';
require_once __DIR__ . '/TemplateEngine.php';

$root = new Elem('html', '', ['lang' => 'en']);
$head = new Elem('head');
$title = new Elem('title', 'Shrek', ['id' => 'main-title']);
$head->pushElement($title);
$meta = new Elem('meta', '', ['charset' => 'UTF-8']);
$head->pushElement($meta);
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
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";
$engine->createFile('./output.html');

// test no html tag
$root = new Elem('h1');
$head = new Elem('head');
$meta = new Elem('meta', '', ['charset' => 'UTF-8']);
$head->pushElement($meta);
$head->pushElement(new Elem('title', 'Shrek'));
$root->pushElement($head);
$body = new Elem('body');
$body->pushElement(new Elem('p', 'Shrek is life.'));
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test no head tag
$root = new Elem('html');
$body = new Elem('body');
$body->pushElement(new Elem('p', 'Shrek is life.'));
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test double title tag
$root = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Shrek'));
$head->pushElement(new Elem('title', 'Ciuchino'));
$meta = new Elem('meta', '', ['charset' => 'UTF-8']);
$head->pushElement($meta);
$root->pushElement($head);
$body = new Elem('body');
$body->pushElement(new Elem('p', 'Shrek is life.'));
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test tag p with children
$root = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Shrek'));
$meta = new Elem('meta', '', ['charset' => 'UTF-8']);
$head->pushElement($meta);
$root->pushElement($head);
$body = new Elem('body');
$ptag = new Elem('p', 'Shrek is life.');
$ptag->pushElement(new Elem('span', 'This is a span inside a p tag.'));
$body->pushElement($ptag);
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test table with h1 children
$root = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Shrek'));
$meta = new Elem('meta', '', ['charset' => 'UTF-8']);
$head->pushElement($meta);
$root->pushElement($head);
$body = new Elem('body');
$body->pushElement(new Elem('h1', 'Shrek is life.'));
$table = new Elem('table');
$h1Head = new Elem('h1');
$table->pushElement($h1Head);
$body->pushElement($table);
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test tr tag children restriction
$root = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Test TR')); 
$head->pushElement(new Elem('meta', '', ['charset' => 'UTF-8']));
$root->pushElement($head);
$body = new Elem('body');
$table = new Elem('table');
$tr = new Elem('tr');
$tr->pushElement(new Elem('td', 'Cell 1'));
$tr->pushElement(new Elem('div', 'Invalid child'));
$table->pushElement($tr);
$body->pushElement($table);
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test ul tag children restriction
$root = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Test UL')); 
$head->pushElement(new Elem('meta', '', ['charset' => 'UTF-8']));
$root->pushElement($head);
$body = new Elem('body');
$ul = new Elem('ul');
$ul->pushElement(new Elem('li', 'Valid item'));
$ul->pushElement(new Elem('p', 'Invalid child'));
$body->pushElement($ul);
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";

// test ol tag children restriction
$root = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Test OL')); 
$head->pushElement(new Elem('meta', '', ['charset' => 'UTF-8']));
$root->pushElement($head);
$body = new Elem('body');
$ol = new Elem('ol');
$ol->pushElement(new Elem('li', 'First valid'));
$ol->pushElement(new Elem('span', 'Invalid child'));
$body->pushElement($ol);
$root->pushElement($body);
echo $root->validPage() ? "Valid HTML page\n" : "Invalid HTML page\n";
?>
