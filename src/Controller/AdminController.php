<?php

namespace App\Controller;

use App\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\CarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function dashboard(AuthorizationCheckerInterface $authChecker): Response
    {
        // Vérifier si l'utilisateur a accès à cette page
        /*if (!$authChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé');
        }*/
        return $this->render('admin/dashboard.html.twig');
    }
}
