<?php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'create:admin',
    description: 'Creates an admin user.',
)]
class CreateAdminCommand extends Command
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        parent::__construct(); // Appel du constructeur parent

        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Logique pour créer l'utilisateur admin
        $this->createAdminUser($output);

        return Command::SUCCESS;
    }

    public function createAdminUser(OutputInterface $output)
    {
        // Logique pour créer l'utilisateur admin
        // Créez une nouvelle instance de l'entité User avec le rôle ROLE_ADMIN
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setRole('admin');
        $admin->setFirstName('Vincent');
        $admin->setLastName('Parrot');

        // Encoder le mot de passe de l'administrateur
        $plainPassword = 'admin123'; // Changez cela en un mot de passe sécurisé
        $encodedPassword = $this->passwordHasher->hashPassword($admin, $plainPassword);
        $admin->setPassword($encodedPassword);

        // Persister l'administrateur dans la base de données
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln('L\'utilisateur administrateur a été créé avec succès.');

        return Command::SUCCESS;
    }
}
