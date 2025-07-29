<?php
include_once './HotBeverage.php';
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

    public function createFile(HotBeverage $text)
    {
        $reflection = new \ReflectionClass($text);
        $fileName = $text->getName() . '.html';
        $templateName = './template.html';
        if (!file_exists($templateName) || !is_readable($templateName))
            throw new Exception("Template file not found or not readable");
        $this->parameters = [];
        $fields = [
            'name'        => 'nom',
            'price'       => 'prix',
            'resistence'  => 'resistance',
            'description' => 'description',
            'comment'     => 'commentaire',
        ];
        $properties = [];
        $classRef = $reflection;
        do 
        {
            foreach ($classRef->getProperties() as $prop)
                $properties[$prop->getName()] = $prop;
        } while ($classRef = $classRef->getParentClass());
        foreach ($properties as $prop)
        {
            $propName = $prop->getName();
            if (isset($fields[$propName]))
            {
                $getter = 'get' . ucfirst($propName);
                if (method_exists($text, $getter))
                    $this->parameters[$fields[$propName]] = $text->$getter();
            }
        }
        $html = file_get_contents($templateName);
        if ($html === false)
            throw new Exception("Failed to read template file: $templateName");
        $html = preg_replace_callback('/\{(\w+)\}/', [$this, 'callback'], $html);
        $this->parameters = [];
        file_put_contents($fileName, $html);
        if (!file_exists($fileName) || !is_writable($fileName))
            throw new Exception("Failed to create or write to file: $fileName");
        echo "File created: $fileName\n";
    }
}
?>