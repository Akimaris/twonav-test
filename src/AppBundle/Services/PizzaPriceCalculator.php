<?php

namespace AppBundle\Services;

use AppBundle\Entity\Admin\Pizza;

class PizzaPriceCalculator
{
    protected $pizza;

    const PREPARATION_PRICE = 1.5;

    public function __construct(Pizza $pizza)
    {
        $this->pizza = $pizza;
    }

    public function calculate()
    {
        $total = 0;
        foreach ($this->pizza->getPizzaIngredients() as $ingredient) {
            $total += $ingredient->getIngredient()->getPrice();
        }

        $total = $this->addPreparationPrice($total);

        return $total;
    }

    public function addPreparationPrice($total)
    {
        return $total * self::PREPARATION_PRICE;
    }
}