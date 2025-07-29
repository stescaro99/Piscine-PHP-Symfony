<?php
include './Text.php';
class TemplateEngine
{
    public function __construct()
    {
    }
    public function __destruct()
    {
    }
    public function createFile($fileName, Text $text)
    {
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POESIA</title>
</head>
<body>
    ' . $text->render_as_html() . '
</body>
</html>';
        file_put_contents($fileName, $html);
    }
}
?>