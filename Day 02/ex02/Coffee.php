<?php
include_once './HotBeverage.php';
class Coffee extends HotBeverage
{
    private $description = 'A tiny cup of pure passion, strong enough to wake the dead and elegant enough to sip standing at a bar. Then came the Americans—with their buckets of coffee-flavored milk and pumpkin nonsense—turning espresso into a dessert.';
    private $comment = 'Mamma mia, what did we do to deserve this? ☕🇮🇹';

    public function __construct()
    {
        parent::__construct("Coffee", 1.20, 2);
    }

    public function __destruct()
    {
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
?>
