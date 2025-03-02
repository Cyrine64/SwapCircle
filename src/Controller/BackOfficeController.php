<?php


namespace App\Controller;
use Symfony\Component\Security\http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface; 
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;




final class BackOfficeController extends AbstractController
{
    private $entityManager;
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger; 
    }

    public function someMethod()
    {
        $this->logger->info('This is a log message.'); 
    }

    #[Route('/dashboard', name: 'app_back_office')]
    public function index(): Response
    {
        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
        ]);
    }
    #[Route('/tableUser', name: 'app_user')]
    public function UserTable(): Response
    {
        return $this->render('back_office/tableuser.html.twig', [
            'controller_name' => 'BackOfficeController',
        ]);
    }
    public function listUsers(UserRepository $userRepository): Response
{
    return $this->render('tableuser.html.twig', [
        'users' => $userRepository->findAll()
    ]);
}


  #[Route('/admin/admins', name:'admins_list')]
 
public function listAdmins(UserRepository $userRepository): Response
{
    return $this->render('back_office/tableAdmin.html.twig', [
        'users' => $userRepository->findAll()
    ]);
}

  
    #[Route('/tableUser', name: 'app_user')]
    public function UserTae(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();  // Fetch all users from the database
        return $this->render('back_office/tableuser.html.twig', [
            'users' => $users,  // Pass 'users' to the template
        ]);
    }
     // Add the toggleUserStatus method

    #[Route('/admin/user/toggle-status/{id}', name: 'toggle_user_status', methods: ['POST'])]
    public function toggleUserStatus(User $user, EntityManagerInterface $entityManager): JsonResponse
{
    // 🚨 Prevent disabling other admins
    if (in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['success' => false, 'message' => 'You cannot disable another admin.'], 403);
    }

    // ✅ Toggle status for non-admin users
    $user->setIsEnabled(!$user->getIsEnabled());
    $entityManager->persist($user);
    $entityManager->flush();

    return new JsonResponse(['success' => true, 'status' => $user->getIsEnabled()]);
}
    #[Route('/admin/user/csrf-token', name: 'csrf_token', methods: ['GET'])]
public function getCsrfToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
{
    return new JsonResponse($csrfTokenManager->getToken('toggle_user')->getValue());
}
    #[Route('/admin/user/roles/{id}', name: 'admin_update_user_roles', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
  
public function updateUserRoles(User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $roles = (array) $request->request->all('roles');

    // Validate roles to prevent privilege escalation
    $validRoles = ['ROLE_USER', 'ROLE_ADMIN'];
    $filteredRoles = array_filter($roles, fn($role) => in_array($role, $validRoles));

    // Ensure at least ROLE_USER is assigned
    if (empty($filteredRoles)) {
        $filteredRoles[] = 'ROLE_USER';
    }

    $user->setRoles($filteredRoles);

    // 🛠️ Fix: Automatically set adminVerified = true if the user becomes an admin
    if (in_array('ROLE_ADMIN', $filteredRoles)) {
        $user->setAdminVerified(true);
    } else {
        $user->setAdminVerified(false);
    }

    $entityManager->flush();

    return new JsonResponse([
        'success' => true,
        'roles' => $filteredRoles,
        'adminVerified' => $user->isAdminVerified(),
        'message' => 'User roles updated successfully.'
    ]);
}
}