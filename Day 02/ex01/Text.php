<?php
class Text
{
    private $strArray;

    public function __construct($strArray)
    {
        $this->strArray = $strArray;
    }

    public function __destruct()
    {
        $this->strArray = null;
    }

    public function add_string($str)
    {
        $this->strArray[] = $str;
    }

    public function render_as_html()
    {
        $html = '';
        foreach ($this->strArray as $str) {
            $html .= '<p>' . htmlspecialchars($str, ENT_QUOTES, 'UTF-8') . '</p>';
        }
        return $html;
    }


}
?>