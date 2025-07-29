<?php

include_once 'MyException.php';
class Elem
{
    private static array $allowedTags = [
        'meta', 'img', 'hr', 'br', 'html', 'head', 'body', 'title',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div', 'table', 'tr', 'td', 'th', 'ul', 'ol', 'li'
    ];
    private string $element;
    private string $content;
    private array $children = [];
    private array $attributes = [];


    public function __construct(string $element, string $content = '', array $attributes = [])
    {
        if (!in_array($element, self::$allowedTags, true))
            throw new MyException("Tag \"$element\" is not supported");
        $this->element = $element;
        $this->content = $content;
        $this->attributes = $attributes;
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
        $attrString = '';
        if (!empty($this->attributes)) {
            $parts = [];
            foreach ($this->attributes as $name => $value)
                $parts[] = sprintf('%s="%s"', htmlspecialchars($name, ENT_QUOTES), htmlspecialchars($value, ENT_QUOTES));
            $attrString = ' ' . implode(' ', $parts);
        }
        $html = "{$indent}<{$this->element}{$attrString}>";
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

    public function validPage(): bool
    {
        if ($this->element !== 'html' || count($this->children) !== 2)
            return false;
        list($head, $body) = $this->children;
        if ($head->element !== 'head' || $body->element !== 'body')
            return false;
        $titleCount = 0;
        $metaCount = 0;
        foreach ($head->children as $child)
        {
            if ($child->element === 'title')
                $titleCount++;
            elseif ($child->element === 'meta' && isset($child->attributes['charset']))
                $metaCount++;
        }
        if ($titleCount !== 1 || $metaCount !== 1)
            return false;
        return $this->validateNode($body);
    }

    private function validateNode(Elem $elem): bool
    {
        $tag = $elem->element;
        if ($tag === 'p' && !empty($elem->children))
            return false;
        else if ($tag === 'table')
        {
            foreach ($elem->children as $c)
            {
                if ($c->element !== 'tr')
                    return false;
            }
        }
        else if ($tag === 'tr') 
        {
            foreach ($elem->children as $c)
            {
                if (!in_array($c->element, ['th', 'td'], true))
                    return false;
            }
        }
        else if (in_array($tag, ['ul', 'ol'], true))
        {
            foreach ($elem->children as $c)
            {
                if ($c->element !== 'li')
                    return false;
            }
        }
        foreach ($elem->children as $c)
        {
            if (!$this->validateNode($c)) 
                return false;
        }
        return true;
    }
}
?>