<?php

namespace App\Controller;


use App\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\CarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/api/occasion', name: 'api_occasion_')]
class OccasionController extends AbstractController
{

    private ManagerRegistry $managerRegistry;
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        $this->managerRegistry = $managerRegistry;
        $this->entityManager = $entityManager;
    }


    #[Route('/cars', name: 'cars_index', methods: ['GET'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function index(CarRepository $carRepository): JsonResponse
    {
        $cars = $carRepository->findAll();
        $carData = [];

        foreach ($cars as $car) {
            $carData[] = [
                'id' => $car->getId(),
                'model' => $car->getModel(),
                'price' => $car->getPrice(),
                'kilometer' => $car->getKilometer(),
                'year' => $car->getYear(),
            ];
        }

        return new JsonResponse($carData, JsonResponse::HTTP_OK);
    }

    #[Route('/cars', name: 'cars_new', methods: ['POST'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function new(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $car = new Car();
        $car->setModel($data['model']);
        $car->setPrice($data['price']);
        $car->setKilometer($data['kilometer']);
        $car->setYear($data['year']);

        // Récupérer le fichier image
        $imageFile = $request->files->get('image');
        
        // Vérifier si une image a été téléchargée
        if ($imageFile instanceof UploadedFile) {
            // Générer un nom de fichier unique
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
            // Déplacer le fichier vers le répertoire où les images sont stockées
            $imageFile->move(
                $this->getParameter('images_directory'), // Chemin vers le répertoire des images configuré dans config/services.yaml
                $newFilename
            );
            
            // Stocker le chemin de l'image dans l'entité Car
            $car->setImg($newFilename);
        }
    
        $entityManager = $this->entityManager;
        $entityManager->persist($car);
        $entityManager->flush();

        return new JsonResponse(['message' => 'New car created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/cars/{id}', name: 'cars_show', methods: ['GET'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function show(int $id, CarRepository $carRepository): JsonResponse
    {
        $car = $carRepository->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }

        return new JsonResponse(['car' => $car], JsonResponse::HTTP_OK);
    }

    #[Route('/cars/{id}', name: 'cars_edit', methods: ['PUT'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function edit(int $id, Request $request, CarRepository $carRepository): JsonResponse
    {
        $car = $carRepository->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['model'])) {
            $car->setModel($data['model']);
        }
        if (isset($data['price'])) {
            $car->setPrice($data['price']);
        }
        if (isset($data['kilometer'])) {
            $car->setKilometer($data['kilometer']);
        }
        if (isset($data['year'])) {
            $car->setYear($data['year']);
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Car with ID ' . $id . ' modified'], JsonResponse::HTTP_OK);
    }

    #[Route('/cars/{id}', name: 'cars_delete', methods: ['DELETE'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function delete(int $id, CarRepository $carRepository): JsonResponse
    {
        $car = $carRepository->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }

        $this->entityManager->remove($car);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Car with ID ' . $id . ' deleted'], JsonResponse::HTTP_OK);
    }
}
