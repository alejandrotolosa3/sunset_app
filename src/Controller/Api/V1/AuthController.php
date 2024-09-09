<?php

namespace App\Controller\Api\V1;

use App\Domain\User\User;
use App\Application\Service\UserService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Errors;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AuthController extends AbstractController
{

    private $validator;
    private $passwordEncoder;
    private $jwtManager;
    private $entityManager;
    private $userService;



    public function __construct(ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $jwtManager, EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }

    public function register(Request $request): JsonResponse
    {
        //Get Data
        $data = json_decode($request->getContent(), true);
        
        //Create User
       $user = $this->userService->createUser($data);

        //If there is an autologin, then
        if ($this->getParameter('auth.auto_login_after_register')) {

            $request = new Request([
                'email' =>  $data['email'] ?? '',
                'password' => $data['password'] ?? ''
            ]);

            return $this->login($request);
        }

        //return User
        return Response::ok([
            "id" => $user->getId(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail()
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        // Get Data
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Obtener Usuario
        $user = $this->entityManager->getRepository(User::class)->findUserByEmailOrUsername($email, $email);


        if ($user &&  $this->passwordEncoder->isPasswordValid($user, $password)) {
            // Generate token JWT
            $token = $this->jwtManager->create($user);

            return new JsonResponse([
                'token' => $token,
                'expires_at' => Carbon::now()->addDays(29),
                'user' => [
                            "id" => $user->getId(),
                            "username" => $user->getUsername(),
                            "email" => $user->getEmail()
                        ]
            ]);
        }

        return new JsonResponse(['error' => 'Invalid credentials'], 401);

        // return  User::login($email, $password);
    }
}
