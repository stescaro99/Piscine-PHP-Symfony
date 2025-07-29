<?php
include_once './HotBeverage.php';
class Tea extends HotBeverage
{
    private $description = 'Because sometimes hot leaf juice in grandma\'s favorite mug is all you need to pretend you\'re classy.';
    private $comment = 'Hot drinks sucks! Who decided sipping leaf soup was refreshing? Probably the same people who thought eating bats and spiders was a good idea.';

    public function __construct()
    {
        parent::__construct("Tea", 2.50, 1);
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
