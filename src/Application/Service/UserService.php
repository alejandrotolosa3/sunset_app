<?php

namespace App\Application\Service;

use App\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Errors;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private $entityManager;
    private $passwordEncoder;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }


    public function createUser(array $data)
    {
        $email = $data['email'] ?? '';
        $plainPassword = $data['password'] ?? '';
        $username = $data['username'] ?? '';

        // Crear Usuario
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $user->setUsername($username);
        $user->setName("TEST");

        // Validar Usuario
        $errors = $this->validator->validate($user);
        $errorsMessages = Errors::getErrors($errors);

        // Si hay errores, retornar los mismos
        if (count($errorsMessages) > 0) {
            //DEVOLVER EXCEPTION DEL PRIMER ERROR;
            return ['status' => 'error', 'errors' => $errorsMessages];
        }

        // Guardar usuario
       $this->entityManager->getRepository(User::class)->add($user, true);

        return $user;
    }
}
