<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry; // Importer la classe ManagerRegistry

#[Route('/api/users', name:'api_users_')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private ManagerRegistry $managerRegistry; // Injecter le ManagerRegistry

    public function __construct(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $managerRegistry)
    {
        $this->passwordHasher = $passwordHasher;
        $this->managerRegistry = $managerRegistry; // Injecter le ManagerRegistry dans le constructeur
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $userData = [];

        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
            ];
        }

        return new JsonResponse($userData, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'new', methods: ['POST'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setFirstName($data['firstname']);
        $user->setLastName($data['lastname']);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);  
            
        // Vérifiez si la clé "role" est présente dans les données JSON
        if (isset($data['role'])) {
            // Définissez le rôle de l'utilisateur uniquement si la clé "role" est présente
            $user->setRole($data['role']);
        } else {
            // Définissez un rôle par défaut si la clé "role" n'est pas présente
            $user->setRole('user');
        }
        $encodedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($encodedPassword);
        
        $entityManager = $this->managerRegistry->getManager(); // Utiliser le ManagerRegistry
        $entityManager->persist($user);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'New user created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function show(int $id): JsonResponse
    {
        $entityManager = $this->managerRegistry->getManager(); // Utiliser le ManagerRegistry
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return new JsonResponse(['user' => $user], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $entityManager = $this->managerRegistry->getManager(); // Utiliser le ManagerRegistry
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'User with ID ' . $id . ' modified'], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->managerRegistry->getManager(); // Utiliser le ManagerRegistry
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User with ID ' . $id . ' deleted'], JsonResponse::HTTP_OK);
    }
}
