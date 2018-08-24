<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * Class CategorieController
     * @package App\Controller
     *
     * @Route("/{id}")
     */
    public function index(Category $category)
    {


        return $this->render('category/index.html.twig', [
            'category' => $category
        ]);
    }

    public function menu()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Category::class);

        $categories = $repository->findAll();

        return $this->render(
            'category/menu.html.twig',
            [
                'categories' => $categories
            ]
        );

    }
}
