<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Admin\Ingredient;
use AppBundle\Entity\Admin\Pizza;
use AppBundle\Entity\Admin\PizzaIngredient;
use AppBundle\Services\PizzaPriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pizza controller.
 *
 */
class PizzaController extends Controller
{
    /**
     * Lists all pizza entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pizzas = $em->getRepository('AppBundle:Admin\Pizza')->findAll();

        return $this->render('admin/pizza/index.html.twig', array(
            'pizzas' => $pizzas,
        ));
    }

    /**
     * Creates a new pizza entity.
     *
     */
    public function newAction(Request $request)
    {
        $pizza = new Pizza();
        $form = $this->createForm('AppBundle\Form\Admin\PizzaType', $pizza);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pizza);
            $em->flush();

            $this->addPizzaIngredients($pizza, $em, $request->get('ingredients'));

            return $this->redirectToRoute('pizza_show', array('id' => $pizza->getId()));
        }

        $ingredients = $this->getDoctrine()->getEntityManager()->getRepository(Ingredient::class)->findAll();
        return $this->render('admin/pizza/new.html.twig', array(
            'pizza' => $pizza,
            'ingredients' => $ingredients,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pizza entity.
     *
     */
    public function showAction(Pizza $pizza)
    {
        $deleteForm = $this->createDeleteForm($pizza);

        return $this->render('admin/pizza/show.html.twig', array(
            'pizza' => $pizza,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pizza entity.
     *
     */
    public function editAction(Request $request, Pizza $pizza)
    {
        $deleteForm = $this->createDeleteForm($pizza);
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

            return $this->redirectToRoute('pizza_index');
        }

        $ingredients = $this->getDoctrine()->getEntityManager()->getRepository(Ingredient::class)->findAll();

        return $this->render('admin/pizza/edit.html.twig', array(
            'pizza' => $pizza,
            'ingredients' => $ingredients,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pizza entity.
     *
     */
    public function deleteAction(Request $request, Pizza $pizza)
    {
        $form = $this->createDeleteForm($pizza);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pizza);
            $em->flush();
        }

        return $this->redirectToRoute('pizza_index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPizzaPriceAction(Request $request)
    {
        $pizza = new Pizza();
        foreach ($request->get('ingredients') as $ingredient) {
            $pizzaIngredient = new PizzaIngredient();
            $pizzaIngredient->setIngredient($this->getDoctrine()->getEntityManager()->getRepository(Ingredient::class)->find($ingredient));
            $pizza->addPizzaIngredients($pizzaIngredient);
        }

        $pizzaPriceCalculator = new PizzaPriceCalculator($pizza);

        return new JsonResponse(["price" => $pizzaPriceCalculator->calculate()]);
    }

    /**
     * Creates a form to delete a pizza entity.
     *
     * @param Pizza $pizza The pizza entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pizza $pizza)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pizza_delete', array('id' => $pizza->getId())))
            ->setMethod('DELETE')
            ->getForm();
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
