<?php
class TemplateEngine
{
    private $parameters = [];

    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    private function callback(array $matches): string
    {
        return isset($this->parameters[$matches[1]]) ? $this->parameters[$matches[1]] : $matches[0];
    } 

    public function createFile($fileName, $templateName, $parameters)
    {
        if (!file_exists($templateName) || !is_readable($templateName))
            throw new Exception("Template file not found or not readable");
        $this->parameters = $parameters;

        $html = file_get_contents($templateName);
        if ($html === false)
            throw new Exception("Failed to read template file: $templateName");
        $html = preg_replace_callback('/\{(\w+)\}/', [$this, 'callback'], $html);
        $this->parameters = [];
        file_put_contents($fileName, $html);
    }
}
?>