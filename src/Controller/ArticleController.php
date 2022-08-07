<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
      $repArticle = $doctrine->getRepository(Article::class);
      return $this->render('article/index.html.twig', [
        'articles' => $repArticle->findBy(array(), array('date_created' => 'DESC')),
      ]);
    }

    #[Route('/article/{id<\d+>}', name: 'show_article')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
      $rep = $doctrine->getRepository(Article::class);
      $article = $rep->find($id);
      return $this->render('article/show_article.html.twig', [
        'article' => $article
      ]);
    }

    #[Route('/supprimer/{id<\d+>}', name: 'delete_article')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
      $rep = $doctrine->getRepository(Article::class);
      $article = $rep->find($id);
      $em = $doctrine->getManager();
      $em->remove($article);
      $em->flush();
      return $this->redirectToRoute('home');
    }

    #[Route('/nouveau', name: 'create_article')]
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
      $article = new Article();
      $form = $this->createForm(ArticleType::class, $article);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($article);
        $em->flush();
        return $this->redirectToRoute('show_article', ['id' => $article->getId()]);
      }

      return $this->render('article/create.html.twig', [
        'form' => $form->createView()
      ]);
    }

    #[Route('/modifier/{id<\d+>}', name: 'edit_article')]
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
      $rep = $doctrine->getRepository(Article::class);
      $article = $rep->find($id);
      $form = $this->createForm(ArticleType::class, $article);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute('show_article', ['id' => $id]);
      }

      return $this->render('article/create.html.twig', [
        'form' => $form->createView()
      ]);
    }
}
