<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user')]
    public function request(Request $request, EntityManagerInterface $entityManager)
    {
        if ($request->getMethod() == "POST") {
            $datas = [];
            $datas[] = $request->get("firstname");
            $datas[] = $request->get("lastname");
            $datas[] = $request->get("address");
            $this->createUser($entityManager, implode(' - ', $datas));
        }

        $action = $request->get("action");
        if ($action == "delete") {
            $id = $request->get("id");
            $this->deleteUser($entityManager, $id);
        }

        $responsitory = $entityManager->getRepository(User::class);
        $users = $responsitory->findAll();

        return $this->render('user.html.twig', [
            'obj' => $request->getMethod(),
            'users' => $users
        ]);
    }

    private function createUser(EntityManagerInterface $entityManager,string $data) {
        $user = new User;
        $user->setData($data);
        $entityManager->persist($user);
        $entityManager->flush();
    }

    private function deleteUser(EntityManagerInterface $entityManager,int $id) {
        $user = $entityManager->getRepository(User::class)->find($id);
        if(!empty($user)) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
    }
}