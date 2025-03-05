<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Reaction;
use App\Entity\CommentaireBlog;
use App\Form\BlogType;
use App\Form\BlogSearchType;
use App\Form\CommentaireBlogType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;

class BlogController extends AbstractController
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/blog', name: 'blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(BlogSearchType::class);
        $form->handleRequest($request);

        $query = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
        }

        $queryBuilder = $blogRepository->findBySearchOrderByLikes($query);
        
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('front_office/blog/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    #[Route('/blog/{id}', name: 'blog_show', methods: ['GET', 'POST'])]
public function show(Blog $blog, Request $request, EntityManagerInterface $entityManager): Response
{
    $commentaire = new CommentaireBlog();
    $form = $this->createForm(CommentaireBlogType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer l'utilisateur avec l'ID 1
        $user = $entityManager->find(Utilisateur::class, 1);
        
        if (!$user) {
            $this->addFlash('error', "L'utilisateur avec l'ID 1 n'existe pas !");
            return $this->redirectToRoute('blog_show', ['id' => $blog->getIdArticle()]);
        }

        // Associer l'utilisateur et le blog au commentaire
        $commentaire->setUser($user);
        $commentaire->setBlog($blog);

        // Sauvegarde en base de données
        $entityManager->persist($commentaire);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire ajouté avec succès');
        return $this->redirectToRoute('blog_show', ['id' => $blog->getIdArticle()]);
    }

    return $this->render('front_office/blog/show.html.twig', [
        'blog' => $blog,
        'commentForm' => $form,
    ]);
}



#[Route('/blog/{id}/react', name: 'blog_react', methods: ['POST'])]
public function react(Blog $blog, Request $request, EntityManagerInterface $entityManager): Response
{
    if (!$this->isCsrfTokenValid('react'.$blog->getIdArticle(), $request->request->get('_token'))) {
        return $this->json(['error' => 'Token CSRF invalide'], 400);
    }

    $user = $entityManager->find(Utilisateur::class, 1); // Remplace par l'utilisateur connecté
    if (!$user) {
        return $this->json(['error' => 'Utilisateur non trouvé'], 403);
    }

    $type = $request->request->get('type');
    if (!in_array($type, ['like', 'dislike'])) {
        return $this->json(['error' => 'Type de réaction invalide'], 400);
    }

    $reaction = $entityManager->getRepository(Reaction::class)->findOneBy([
        'user' => $user,
        'blog' => $blog
    ]);

    if ($reaction) {
        if ($reaction->getType() === $type) {
            return $this->json(['error' => 'Vous avez déjà réagi avec ce type'], 400);
        } else {
            $reaction->setType($type);
            $entityManager->flush();
            return $this->json([
                'message' => $type === 'like' ? 'Réaction changée en Like' : 'Réaction changée en Dislike',
                'likes' => count($entityManager->getRepository(Reaction::class)->findBy(['blog' => $blog, 'type' => 'like'])),
                'dislikes' => count($entityManager->getRepository(Reaction::class)->findBy(['blog' => $blog, 'type' => 'dislike']))
            ]);
        }
    }

    $reaction = new Reaction();
    $reaction->setUser($user)->setBlog($blog)->setType($type);
    $entityManager->persist($reaction);
    $entityManager->flush();

    return $this->json([
        'message' => $type === 'like' ? 'Like ajouté' : 'Dislike ajouté',
        'likes' => count($entityManager->getRepository(Reaction::class)->findBy(['blog' => $blog, 'type' => 'like'])),
        'dislikes' => count($entityManager->getRepository(Reaction::class)->findBy(['blog' => $blog, 'type' => 'dislike']))
    ]);
}

    #[Route('/blog/comment/{id}/edit', name: 'comment_edit', methods: ['POST'])]
    public function editComment(Request $request, CommentaireBlog $comment, EntityManagerInterface $entityManager): Response
    {
        $content = $request->request->get('content');
        if (!empty($content)) {
            $comment->setContenu($content);
            $entityManager->flush();
            $this->addFlash('success', 'Comment updated successfully');
        }
        
        return $this->redirectToRoute('blog_show', ['id' => $comment->getBlog()->getIdArticle()]);
    }

    #[Route('/blog/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function deleteComment(CommentaireBlog $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $blogId = $comment->getBlog()->getIdArticle();
        
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment deleted successfully');
        }

        return $this->redirectToRoute('blog_show', ['id' => $blogId]);
    }

    // Back Office Routes
    #[Route('/admin/blog', name: 'admin_blog_index', methods: ['GET'])]
    public function adminIndex(BlogRepository $blogRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $blogRepository->createQueryBuilder('b')
            ->orderBy('b.date_publication', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2 // Number of items per page
        );

        return $this->render('back_office/blog/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/admin/blog/new', name: 'admin_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $this->doctrine->resetManager();

        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('blog_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                    return $this->redirectToRoute('admin_blog_new');
                }

                $blog->setImage($newFilename);
            }

            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('back_office/blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/admin/blog/{id}/edit', name: 'admin_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('blog_images_directory'),
                        $newFilename
                    );
                    $blog->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('back_office/blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/admin/blog/{id}', name: 'admin_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getIdArticle(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
            $this->addFlash('success', 'Article supprimé avec succès');
        }

        return $this->redirectToRoute('admin_blog_index');
    }

    #[Route('/admin/blog/comment/{id}/delete', name: 'admin_comment_delete', methods: ['POST'])]
    public function deleteCommentAdmin(CommentaireBlog $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $blogId = $comment->getBlog()->getIdArticle();
        
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire supprimé avec succès');
        }

        return $this->redirectToRoute('admin_blog_index');
    }
}
