<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\UserRepository;



/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password before saving
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);

            // Ensure at least ROLE_USER is assigned
            $roles = $form->get('roles')->getData();
            if (empty($roles)) {
                $roles = ['ROLE_USER'];
            }
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();
            $this->logger->info("User {$user->getEmail()} created by admin with roles: " . implode(', ', $roles));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
 * @Route("/admin/users", name="users_list")
 */


    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }
    #[Route('/profile/edit/{id}', name: 'edit_user', methods: ['GET', 'POST']),]

    #[Route('/profile/edit/{id}', name: 'edit_user')]
    public function editUser(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException("You can only edit your own profile.");
        }
    
        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setLastName($request->request->get('lastName'));
            $user->setEmail($request->request->get('email'));
    
            $newPassword = $request->request->get('password');
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('user_profile');
        }
    
        return $this->render('user/edit.html.twig', ['user' => $user]);
    }
    #[Route('/profile/delete/{id}', name: 'delete_user')]

    /*public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->logger->info("User {$user->getEmail()} deleted.");
        }

        return $this->redirectToRoute('app_user_index');
    }*/
    public function deleteUser(
        User $user, 
        EntityManagerInterface $entityManager, 
        TokenStorageInterface $tokenStorage, 
        SessionInterface $session, 
        LoggerInterface $logger
    ): Response {
        // Ensure the user is deleting their own account
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException("You can only delete your own account.");
        }
    
        try {
            // Log user ID before deletion
            $logger->info("Deleting user with ID: " . $user->getid());
    
            // Remove the user from the database
            $entityManager->remove($user);
            $entityManager->flush();
    
            // Log successful deletion
            $logger->info("User with ID " . $user->getid() . " successfully deleted.");
    
            // Manually log out the user AFTER deleting the account
            $tokenStorage->setToken(null); // Remove authentication token
            $session->invalidate(); // Destroy session
    
            // Redirect to home page
            return $this->redirectToRoute('app_front_office');
        } catch (\Exception $e) {
            // Log error message
            $logger->error("Error deleting user with ID " . $user->getid() . ": " . $e->getMessage());
    
            // Redirect back to profile in case of an error
            return $this->redirectToRoute('user_show');
        }
    }

   
    public function toggleUserStatus(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        LoggerInterface $logger
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Ensure admin access
    
        // Retrieve CSRF token from request
        $submittedToken = $request->request->get('_token');
    
        // Validate CSRF token
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('toggle_status', $submittedToken))) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
        }
        if (!$user) {
            $logger->error('User not found!');
            return new JsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }
        // Toggle user status
        $user->setIsEnabled(!$user->getIsEnabled());
        $entityManager->persist($user);
        $entityManager->flush();
    
        $logger->info("User ID {$user->getId()} status updated to: " . ($user->getIsEnabled() ? 'Enabled' : 'Disabled'));
    
        return new JsonResponse([
            'success' => true,
            'status' => $user->getIsEnabled(),
            'message' => 'User status updated successfully.'
        ]);
    }
    #[Route('/admin/user/csrf-token', name: 'get_csrf_token', methods: ['GET'])]
public function getCsrfToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse {
    return new JsonResponse($csrfTokenManager->getToken('toggle_status')->getValue());
}
    #[Route('/profile', name: 'user_profile')]
public function profile(): Response
{
    // Get the currently logged-in user
    $user = $this->getUser();

    // Redirect to login if not authenticated
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    return $this->render('user/profile.html.twig', [
        'user' => $user,
    ]);}
}
