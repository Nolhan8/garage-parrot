<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/feedbacks')]
class FeedbackController
{
    private $entityManager;
    private $feedbackRepository;

    public function __construct(EntityManagerInterface $entityManager, FeedbackRepository $feedbackRepository)
    {
        $this->entityManager = $entityManager;
        $this->feedbackRepository = $feedbackRepository;
    }

    #[Route('/', name: 'api_feedbacks_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $feedbacks = $this->feedbackRepository->findAll();
        $feedbackData = [];

        foreach ($feedbacks as $feedback) {
            $feedbackData[] = [
                'id' => $feedback->getId(),
                'authorName' => $feedback->getPostedBy(),
                'rating' => $feedback->getRating(),
                'message' => $feedback->getComment(),
                'moderationStatus' => $feedback->getModerationStatus(),
            ];
        }

        return new JsonResponse($feedbackData, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'api_feedbacks_new', methods: ['POST'])]
    public function new(Request $request, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $data = $request->request->all();
    
        // Vérifier si les clés nécessaires existent dans le tableau $data
        if (!isset($data['authorName']) || !isset($data['rating']) || !isset($data['message'])) {
            return new RedirectResponse($urlGenerator->generate('feedback'));
        }
    
        $feedback = new Feedback();
        $feedback->setPostedBy($data['authorName']);
        $feedback->setRating($data['rating']);
        $feedback->setComment($data['message']);
        $feedback->setModerationStatus('unverified');
    
        $this->entityManager->persist($feedback);
        $this->entityManager->flush();
    
        // Rediriger vers la même page après le traitement du formulaire
        return new RedirectResponse($urlGenerator->generate('feedback'));
    }

    #[Route('/{id}', name: 'api_feedbacks_approve', methods: ['PUT'])]
    public function approve(int $id): JsonResponse
    {
        $feedback = $this->feedbackRepository->find($id);

        if (!$feedback) {
            throw $this->createNotFoundException('Feedback not found');
        }

        $feedback->setModerationStatus('approved');
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Feedback with ID ' . $id . ' approved'], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_feedbacks_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->feedbackRepository->find($id);

        if (!$feedback) {
            throw $this->createNotFoundException('Feedback not found');
        }

        $this->entityManager->remove($feedback);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Feedback with ID ' . $id . ' deleted'], JsonResponse::HTTP_OK);
    }

}
