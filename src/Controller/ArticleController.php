<?php

namespace App\Controller;


use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/article")
 */
class ArticleController extends AbstractController
{

    /**
     * @Route("/{id}")
     */
    public function index(Article $article,Request $request ){

        /* Sous l'article, si l'utilisateur nest pas connecté,
         * l'inviter à le faire pour pouvoir écrire un commentaire
         * Sinon lui affichier un textarea pour pouvoir écrire
         * un commentaire.
         * Nécessite une entité Comment :
         * - content (text en bdd)
         * - publication date (datetime)
         * - user (l'utilisateur qui écrit l'article)
         * - article (l'article sur lequel il a écrit le commentaire)
         * Nécessite le form type qui va avec, contenant le textarea
         *
         * Lister en dessous les commentaires faits sur l'article
         * avec nom utilisateur, date de publication et contenu du message
         *
         */
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment );

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()) {
               $comment
                     ->setUser($this->getUser())
                     ->setArticle($article)
               ;

                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $this->addFlash(
                    'succes',
                    'votre commentaire est enregistré'
                );

                return $this->redirectToRoute(
                    $request -> get('_route'),
                    [
                        'id' =>$article -> getId()
                    ]
                    );
            }else {
                $this->addFlash(
                  'error',
                  'le formulaire contient des erreurs'
                );
            }

        }


        return $this->render(

            'article/index.html.twig',
            [
                'article' =>$article,
                'form' => $form->createView(),

            ]

        );
    }

}