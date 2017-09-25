<?php

namespace AppBundle\Entity\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pizza
 *
 * @ORM\Table(name="pizza")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PizzaRepository")
 */
class Pizza
{
    public function __construct()
    {
        $this->pizzaIngredients = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\Admin\PizzaIngredient", mappedBy="pizza") */
    private $pizzaIngredients;

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
     * Set name
     *
     * @param string $name
     * @return Pizza
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Pizza
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return ArrayCollection|PizzaIngredient[]
     */
    public function getPizzaIngredients()
    {
        return $this->pizzaIngredients;
    }

    /**
     * @param mixed $pizzaIngredients
     */
    public function setPizzaIngredients($pizzaIngredients)
    {
        $this->pizzaIngredients = $pizzaIngredients;
    }

    /**
     * @param PizzaIngredient $ingredient
     */
    public function addPizzaIngredients(PizzaIngredient $ingredient)
    {
        if ($this->pizzaIngredients->contains($ingredient)) {
            return;
        }
        $this->pizzaIngredients[] = $ingredient;
    }

    /**
     * @param PizzaIngredient $ingredient
     */
    public function removePizzaIngredient(PizzaIngredient $ingredient)
    {
        if (!$this->pizzaIngredients->contains($ingredient)) {
            return;
        }
        $this->pizzaIngredients->removeElement($ingredient);
    }

    /**
     * @param Ingredient $ingredient
     * @return bool
     */
    public function hasIngredient(Ingredient $ingredient)
    {
        foreach ($this->pizzaIngredients as $pizzaIngredient) {
            if ($ingredient == $pizzaIngredient->getIngredient()) {
                return true;
            }
        }

        return false;
    }


}
