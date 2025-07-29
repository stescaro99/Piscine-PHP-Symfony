<?php
class Elem
{
    private static array $allowedTags = [
        'meta', 'img', 'hr', 'br', 'html', 'head', 'body', 'title',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div'
    ];
    private string $element;
    private string $content;
    private array $children = [];


    public function __construct(string $element, string $content = '')
    {
        if (!in_array($element, self::$allowedTags, true))
            throw new Exception("Tag \"$element\" is not supported");
        $this->element = $element;
        $this->content = $content;
    }

    public function __destruct()
    {
    }

    public function pushElement(Elem $element): void
    {
        $this->children[] = $element;
    }

    public function getHTML(int $tabs = 0): string
    {
        $indent = str_repeat("    ", $tabs);
        $html = "{$indent}<{$this->element}>";
        if ($this->content !== '')
            $html .= $this->content;
        else
            $html .= "\n";
        foreach ($this->children as $child)
            $html .= $child->getHTML($tabs + 1);
        if ($this->content === '')
            $html .= "{$indent}";
        $html .= "</{$this->element}>\n";
        return $html;
    }
}
?>