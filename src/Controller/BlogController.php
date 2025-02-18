<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BlogRepository;
use App\Entity\Commentaire;
use App\Entity\BlogLike;



#[Route('/blog')]
final class BlogController extends AbstractController
{
    #[Route(name: 'app_blog_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $blogs = $entityManager
            ->getRepository(Blog::class)
            ->findAll();

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    #[Route('/article',name: 'app_blog_article', methods: ['GET'])]
    public function article(EntityManagerInterface $entityManager): Response
    {
        $blogs = $entityManager
            ->getRepository(Blog::class)
            ->findAll();

        return $this->render('blog/article.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    #[Route('/blog/{id}/like', name: 'blog_like', methods: ['POST'])]
    public function like(Blog $blog, EntityManagerInterface $em): Response
    {

        $like = new BlogLike();
        $like->setArticle($blog);
        $like->setAction('like');

        $em->persist($like);
        $em->flush();

        return $this->redirectToRoute('app_blog_article');
    }

    #[Route('/blog/{id}/dislike', name: 'blog_dislike', methods: ['POST'])]
    public function dislike(Blog $blog, EntityManagerInterface $em): Response
    {

        $dislike = new BlogLike();
        $dislike->setArticle($blog);
        $dislike->setAction('dislike');

        $em->persist($dislike);
        $em->flush();

        return $this->redirectToRoute('app_blog_article');
    }

    #[Route('/{id}/comment', name: 'blog_comment', methods: ['POST'])]
    public function comment(Request $request, Blog $blog, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour commenter.');
        return $this->redirectToRoute('app_blog_article');  // Redirection vers la page de connexion
    }

    $content = $request->request->get('comment_content');

    if (empty($content)) {
        $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
        return $this->redirectToRoute('app_blog_article', ['id' => $blog->getIdArticle()]);
    }

    $comment = new Commentaire();
    $comment->setArticle($blog);
    $comment->setUtilisateur($user);
    $comment->setContenu($content);
    $comment->setDateCommentaire(new \DateTime());

    $em->persist($comment);
    $em->flush();

    $this->addFlash('success', 'Votre commentaire a été ajouté.');

    return $this->redirectToRoute('app_blog_article', ['id' => $blog->getIdArticle()]);
}

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // Créer une nouvelle instance de l'entité Blog
    $blog = new Blog();
    
    // Créer le formulaire pour l'entité Blog
    $form = $this->createForm(BlogType::class, $blog);
    
    // Traiter la requête HTTP (GET ou POST)
    $form->handleRequest($request);

    // Si le formulaire a été soumis et qu'il est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le fichier téléchargé
        $imageFile = $form->get('imageFile')->getData();

        // Si un fichier a été téléchargé
        if ($imageFile) {
            // Générer un nom unique pour le fichier
            $filename = uniqid() . '.' . $imageFile->guessExtension();

            try {
                // Déplacer le fichier vers le répertoire souhaité
                $imageFile->move(
                    $this->getParameter('images_directory'), // Dossier de destination (défini dans services.yaml)
                    $filename
                );

                // Enregistrer le nom du fichier dans la base de données
                $blog->setImage($filename);
            } catch (FileException $e) {
                // Gérer les erreurs de téléchargement de fichier
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                return $this->redirectToRoute('app_blog_new');
            }
        }

        // Persister et enregistrer les données dans la base de données
        $entityManager->persist($blog);
        $entityManager->flush();

        // Rediriger l'utilisateur vers la page d'index des blogs après avoir créé le blog
        $this->addFlash('success', 'L\'article a été ajouté avec succès!');
        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }

    // Rendre le formulaire et la vue si le formulaire n'a pas encore été soumis ou est invalide
    return $this->render('blog/new.html.twig', [
        'form' => $form->createView(), // Il faut passer la vue du formulaire
        'blog' => $blog, // Passer l'objet blog à la vue (au cas où tu en aurais besoin pour l'affichage)
    ]);
}


#[Route('/{idArticle}/show', name: 'app_blog_show', methods: ['GET'])]
public function show(int $idArticle, BlogRepository $blogRepository): Response
{
    $blog = $blogRepository->find($idArticle);

    if (!$blog) {
        throw $this->createNotFoundException('Article not found');
    }

    return $this->render('blog/show.html.twig', [
        'blog' => $blog,
    ]);
}

#[Route('/{idArticle}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
public function edit(int $idArticle, Request $request, BlogRepository $blogRepository, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'article depuis la base de données
    $blog = $blogRepository->find($idArticle);

    // Vérification si l'article existe
    if (!$blog) {
        throw $this->createNotFoundException('L\'article avec l\'ID ' . $idArticle . ' n\'a pas été trouvé.');
    }

    // Créer le formulaire en liant l'entité Blog
    $form = $this->createForm(BlogType::class, $blog);
    $form->handleRequest($request);

    // Vérification si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Vérification si un nouveau fichier image est uploadé
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            // Générer un nom de fichier unique
            $filename = uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move(
                    $this->getParameter('images_directory'), // Le dossier de destination
                    $filename
                );
                $blog->setImage($filename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                return $this->redirectToRoute('app_blog_edit', ['idArticle' => $blog->getIdArticle()]);
            }
        }

        // Sauvegarder les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'L\'article a été mis à jour avec succès!');
        return $this->redirectToRoute('app_blog_index');
    }

    // Rendu de la vue du formulaire
    return $this->render('blog/edit.html.twig', [
        'form' => $form->createView(),
        'blog' => $blog
    ]);
}


#[Route('/{idArticle}', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getIdArticle(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/blog/add', name: 'app_blog_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setDatePublication(new \DateTime()); // Ajoute la date actuelle
            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Article ajouté avec succès !');

            return $this->redirectToRoute('app_blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
