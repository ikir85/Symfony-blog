<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 13/08/2018
 * Time: 10:28
 */

namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller\Admin
 * @Route("/article")
 */

class ArticleController extends Controller
{
  /*
   * faire la page qui liste les articles dans tableau html
   * avec nom de la category et le nom de l'auteur
   * et la date au format francais
   *
   *
   */

    /**
     * @Route ("/")
     */
    public function index(){

        $em = $this -> getDoctrine() -> getManager();
        $repository = $em->getRepository(Article::class);
        $articles = $repository->findAll();
        //toutes les categories triées par id croissant
        $articles =$repository->findBy([], ['id' => 'asc']);

        return $this->render(
            'Admin/article/index.html.twig',
            [
                'articles' =>$articles
            ]

        );

    }

    /**
     * @param Request $request
     *
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id"="\d+"}))
     */
    public function edit(Request $request, $id){
      /*
       * Faire rendu du formulaire et son traitement
       * mettre un lien ajouter dans la page liste
       *
       * Validation: tous les champs obligatoires
       *
       * En création :
       * setter l'auteur avec l'utlisateur connecté $this->getUser() depuis le controleur
       * Mettre date de publication article a maintenant
       *
       * Adapter la route et le contenu de la méthode pour faire fonctionner la page en modification
       * et ajouter le bouton mofidier dans la page liste
       *
       * Enregistrer L'article en bdd si le formulaire est correctement rempli
       * puis rediriger vers la liste avec un message de confirmation
       */
        $em = $this -> getDoctrine() -> getManager();
        $originalImage = null;




        if(is_null($id)) {
            $article = new Article();

            $article ->setAuthor($this->getUser());
             //si la date de publication est definie dans le contrusteur de la class artilcle, pas besoin de le setter
            //$article ->setPublicationDate( new \DateTime() );


        }else {
            $article = $em->find(Article::class, $id);

            if (!is_null($article->getImage())) {

                $originalImage = $article->getImage();

                // on sette l'image avec un objet File pour le traitement par le formulaire
                $article->setImage(
                    new File($this->getParameter('upload_dir') . $originalImage)
                );
            }

            //404 si l'id recu dans l'url est pas en bdd
            if (is_null($article)){
                throw new NotFoundHttpException();
            }
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted()){

           if($form->isValid()) {

               /** @var UploadedFile|null*/
               $image = $article->getImage();

               //s'il y a une image uploadée
               if(!is_null($image)){
                   $filename = uniqid(). '.'. $image->guessExtension();

                   $image -> move(
                       $this->getParameter('upload_dir'),
                       //nom du fichier
                       $filename
                   );

                   // on sette l'attribut image de l'article avec le nom
                   // de l'image pour enregistrement en bdd
                   $article->setImage($filename);

                   if(!is_null($originalImage)){
                       unlink($this->getParameter('upload_dir').$originalImage);
                   }

               } else {
                   //sans upload, pour la modification, on sette l'attribut image de l'article avec le nom d'image venant
                   //de la bdd
                   $article->setImage($originalImage);
               }

               $em->persist($article);
               $em->flush();


               $this->addFlash(
                   'success',
                   'L\'article est enregistré'
               );
               return $this->redirectToRoute('app_admin_article_index');

           } else{
               $this->addFlash(
                   'error',
                   'Le formulaire contient des erreurs'
               );
           }
        }

      return $this->render(
          'Admin/article/edit.html.twig',
          [
              'form' => $form->createView(),
              'original_image' => $originalImage
          ]
      );
    }


    /**
     * @Route("/supression/{id}")
     */
    public function delete(Article $article)
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($article);

        $em->flush();

        // message de confirmation
        $this->addFlash(
            'success',
            'L\'article est supprimé'
        );


        return $this->redirectToRoute('app_admin_article_index');
    }

    }