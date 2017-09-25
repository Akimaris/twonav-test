<?php

namespace AppBundle\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;

/**
 * PizzaIngredient
 *
 * @ORM\Table(name="pizza_ingredient")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PizzaIngredientRepository")
 */
class PizzaIngredient
{

    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="integer")
     */
    private $sorting;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Pizza", inversedBy="pizzaIngredients")
     * @ORM\JoinColumn(name="pizza_id", referencedColumnName="id", nullable=false,onDelete="CASCADE")
     */
    private $pizza;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Ingredient", inversedBy="pizzaIngredients")
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id", nullable=false,onDelete="CASCADE")
     */
    private $ingredient;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sorting
     *
     * @param integer $sorting
     * @return PizzaIngredient
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Get sorting
     *
     * @return integer
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @return mixed
     */
    public function getPizza()
    {
        return $this->pizza;
    }

    /**
     * @param mixed $pizza
     */
    public function setPizza($pizza)
    {
        $this->pizza = $pizza;
    }

    /**
     * @return mixed
     */
    public function getIngredient()
    {
        return $this->ingredient;
    }

    /**
     * @param mixed $ingredient
     */
    public function setIngredient($ingredient)
    {
        $this->ingredient = $ingredient;
    }


}
