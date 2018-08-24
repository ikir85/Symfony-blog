<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 09/08/2018
 * Time: 12:08
 */

namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/")
     */
    public function index(){
        $em = $this -> getDoctrine() -> getManager();
        $repository = $em->getRepository(Category::class);
        $categories = $repository->findAll();
        //toutes les categories triées par id croissant
        $categories =$repository->findBy([], ['id' => 'asc']);

        return $this->render(
            'Admin/category/index.html.twig',
            [
                'categories' =>$categories
            ]

        );

    }

    /**
     * @param Request $request
     * {id} est optionnel grâce à defaults et doit être un nombre (requirements)
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, $id){
        $em = $this -> getDoctrine() -> getManager();

        if(is_null($id)) {
            $category = new Category();
        }else{ //modification
          $category=$em->find(Category::class, $id);
          //404 si l'id recu dans l'url est pas en bdd
          if (is_null($category)){
              throw new NotFoundHttpException();
          }
        }

        //création d'un formulaire lié à la catégorie
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted()){
           //les attributs de l'objet Category ont été settés
           //à partir des champs de formulaires
           // dump($category);

            //valide la saisie du forlulaire a partir
            //des annotations Assert dans l'entité Category
           if($form->isValid()) {

               $em->persist($category);
               $em->flush();

               // message de confirmation
               $this->addFlash(
                   'success',
                   'La catégorie est enregistrée'
               );

               //redirection vers la page de liste
               return $this->redirectToRoute('app_admin_category_index');

           } else{
             $this->addFlash(
                 'error',
                  'Le formulaire contient des erreurs'
             );
           }
        }
        return $this->render(
            'Admin/category/edit.html.twig',
            [
                'form'=>$form->createView()
            ]
        );
    }

    /**
     *
     * @Route("/supression/{id}")
     *
     */
    public function delete(Category $category)
    {
        if ($category->getArticles()->isEmpty()){
        $em = $this -> getDoctrine() -> getManager();
        //préparation de la supression en bdd
        $em->remove($category);
        // supression effective
        $em->flush();

        // message de confirmation
        $this->addFlash(
            'success',
            'La catégorie est supprimée'
        );
        } else {
            $this->addFlash(
              'error',
              'La category ne peut pas etre supprimée car elle contient des articles'
            );
        }

        //redirection vers la page de liste
        return $this->redirectToRoute('app_admin_category_index');

    }
}