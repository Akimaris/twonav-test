<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin\Ingredient;
use AppBundle\Entity\Admin\Pizza;
use AppBundle\Entity\Admin\PizzaIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $pizzas = $this->getRepository(Pizza::class)->findAll();

        return $this->render('default/index.html.twig', array(
            'pizzas' => $pizzas
        ));
    }

    /**
     * @param Request $request
     * @param Pizza $pizza
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function customizeAction(Request $request, Pizza $pizza)
    {
        $editForm = $this->createForm('AppBundle\Form\Admin\PizzaType', $pizza);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($pizza->getPizzaIngredients()) {
                foreach ($pizza->getPizzaIngredients() as $pizzaIngredient) {
                    $em->remove($pizzaIngredient);
                }
            }

            $em->persist($pizza);
            $em->flush();

            $this->addPizzaIngredients($pizza, $em, $request->get('ingredients'));

            return $this->redirectToRoute('home_pizza_index');
        }

        $ingredients = $this->getDoctrine()->getEntityManager()->getRepository(Ingredient::class)->findAll();

        return $this->render('default/edit_pizza.html.twig', array(
            'pizza' => $pizza,
            'ingredients' => $ingredients,
            'edit_form' => $editForm->createView()
        ));
    }

    public function viewAction(Pizza $pizza)
    {
        return $this->render('default/view_pizza.html.twig', array(
            'pizza' => $pizza,
        ));
    }

    private function getRepository($class)
    {
        return $this->getDoctrine()->getManager()->getRepository($class);
    }

    /**
     * @param Pizza $pizza
     * @param $em
     * @param $pizzaIngredients
     */
    private function addPizzaIngredients(Pizza &$pizza, $em, $pizzaIngredients)
    {
        foreach ($pizzaIngredients as $ingredient) {
            $ingredientObject = $this->getDoctrine()->getEntityManager()->getRepository(Ingredient::class)->find($ingredient['id']);

            $pizzaIngredient = new PizzaIngredient();
            $pizzaIngredient->setSorting($ingredient['sorting']);
            $pizzaIngredient->setIngredient($ingredientObject);
            $pizzaIngredient->setPizza($pizza);

            $em->persist($pizzaIngredient);
            $pizza->addPizzaIngredients($pizzaIngredient);
        }
        $em->flush();
    }
}
