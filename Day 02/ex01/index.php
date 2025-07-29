<?php
include './TemplateEngine.php';

$text = new Text(['Se ni\' mondo esistesse un po\' di bene', 'e ognun si honsiderasse suo fratello', 'ci sarebbe meno pensieri e meno pene']);
$text->add_string('e il mondo ne sarebbe assai più bello');
$templateEngine = new TemplateEngine();
$templateEngine->createFile('output.html', $text);
unset($text);
unset($templateEngine);
if (file_exists('output.html'))
    echo "File created successfully: output.html";
else
    echo "Failed to create file.";
?>
