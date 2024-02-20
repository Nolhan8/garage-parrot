<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer les erreurs de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Récupérer le dernier nom d'utilisateur (c'est-à-dire l'username)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Afficher le formulaire de connexion avec les erreurs éventuelles
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/check-login', name: 'check_login', methods: ['POST'])]
    public function checkLogin(Request $request, UserPasswordHasherInterface $passwordHasher, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        // Récupérer les données du formulaire
        $username = $request->request->get('_username');
        $plainPassword = $request->request->get('_password');

        // Récupérer l'EntityManager depuis le registre du gestionnaire
        $entityManager = $this->managerRegistry->getManager();

        // Récupérer l'utilisateur depuis la base de données en utilisant l'username
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => $username]);


        // Vérifier si l'utilisateur existe et si le mot de passe est valide
        if (!$user || !$passwordHasher->isPasswordValid($user, $plainPassword)) {
            // Rediriger avec un message d'erreur
            $this->addFlash('error', 'Invalid username or password');
            return $this->redirectToRoute('app_login');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->redirectToRoute('admin_dashboard');
        } elseif (in_array('ROLE_USER', $roles, true)) {
            return $this->redirectToRoute('user_dashboard');
        }

        // Redirection par défaut si aucun rôle correspondant n'est trouvé
        return $this->redirectToRoute('app_default_home');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
