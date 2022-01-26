<?php

namespace App\Controller;


 use App\Entity\User;
 use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use App\Controller\ApiController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 use Symfony\Component\Security\Core\User\UserInterface;
 use Doctrine\Persistence\ManagerRegistry;
 use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

 class AuthController extends ApiController
 {

  public function register(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher)
  {
      
   $em = $doctrine->getManager();
   $request = $this->transformJsonBody($request);
   $username = $request->get('username');
   $password = $request->get('password');
   $email = $request->get('email');
    
   if (empty($username) || empty($password) || empty($email)){
    return $this->respondValidationError("Invalid Username or Password or Email");
   }


   $user = new User($email);
   $hashedPassword = $passwordHasher->hashPassword($user,$password);
   $user->setPassword($hashedPassword);
   $user->setEmail($email);
   $user->setRoles([1=>'fggf']);
   $em->persist($user);
   $em->flush();
   return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getEmail()));
  }

  /**
   * @param UserInterface $user
   * @param JWTTokenManagerInterface $JWTManager
   * @return JsonResponse
   */
  public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
  {
   return new JsonResponse(['token' => $JWTManager->create($user)]);
  }

 }