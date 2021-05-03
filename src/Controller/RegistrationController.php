<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * RegistrationController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $this->userRepository->findBy([
            'username' => $username
        ]);

        if(!empty($user)){
            return new Response("User already exists", Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setRoles([
            'ROLE_USER'
        ]);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response("User created", Response::HTTP_CREATED);
    }
}
