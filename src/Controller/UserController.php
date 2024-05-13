<?php

namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;



#[Route('/dashboard')]
class UserController extends AbstractController
{
    #[Route('/user', name: 'users_list', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Admin/usersList.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('users_list');
        }

        try {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'There was an error deleting the user.');
        }

        return $this->redirectToRoute('users_list');
    }

    #[Route('/user/ban/{id}', name: 'user_ban', methods: ['POST'])]
        public function banUser(EntityManagerInterface $entityManager, $id): Response
        {
            $user = $entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                $this->addFlash('error', 'User not found.');
            } else {
                $user->setStatus(true); // Ban the user
                $entityManager->flush();
                $this->addFlash('success', 'User has been banned successfully.');
            }

            return $this->redirectToRoute('users_list');
        }

        #[Route('/user/unban/{id}', name: 'user_unban', methods: ['POST'])]
            public function unbanUser(EntityManagerInterface $entityManager, $id): Response
            {
                $user = $entityManager->getRepository(User::class)->find($id);

                if (!$user) {
                    $this->addFlash('error', 'User not found.');
                } else {
                    $user->setStatus(false); // Unban the user
                    $entityManager->flush();
                    $this->addFlash('success', 'User has been unbanned successfully.');
                }

                return $this->redirectToRoute('users_list');
            }
}
