<?php

namespace App\Controller\Admin;


use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class CommentController
 * @package App\Controller\Admin
 * @Route("/commentaire")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/article/{id}")
     */
   public function index(Article $article)
   {
       return $this->render(
           'admin/comment/index.html.twig',
           [
               'article' => $article
           ]
       );
   }
}