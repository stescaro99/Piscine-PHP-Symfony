<?php
class TemplateEngine
{
    private Elem $root;

    public function __construct(Elem $root)
    {
        $this->root = $root;
    }

    public function __destruct()
    {
    }

    public function createFile(string $fileName): void
    {
        $html = $this->root->getHTML();
        file_put_contents($fileName, $html);
    }
}
?>