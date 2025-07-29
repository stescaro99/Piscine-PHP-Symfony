<?php
class HotBeverage
{
    private $name;
    private $price;
    private $resistence;
    
    public function __construct(string $name, float $price, float $resistence)
    {
        $this->name = $name;
        $this->price = $price;
        $this->resistence = $resistence;
    }

    public function __destruct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getResistence(): float
    {
        return $this->resistence;
    }
}
?>