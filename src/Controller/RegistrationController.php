<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use App\Service\JWTTokenService;
use App\Service\SendMailService;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(JWTTokenService $jwtTokenService , SendMailService $mailService,Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            
            // Creation du header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];
            // Creation du payload
            $payload = [
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
            ];
            //Generation du token
            $jwtToken = $jwtTokenService->generateToken(
                $header,
                $payload,
                $this->getParameter('app.jwtsecretkey')
            );
            //dd($jwtToken);
            //on envoi le message
            $mailService->sendMail(
                'no-reply@feulefackindustrie.com',
                $user->getEmail(),
                'Activation de compte chez Feulefack Industrie',
                'newUser',
                compact('user','jwtToken')
            );
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{jwtToken}', name:'app_verify')]
    public function verify(UtilisateurRepository $usersRepo, EntityManagerInterface $em, JWTTokenService $jwts, $jwtToken):Response
    {
        //dd($jwts->checToken($jwtToken, $this->getParameter('app.jwtsecretkey')));
        /**
         * On verifie si le token est valide,
         * le token n'a pas expirer,
         * le token n'a pas ete modifie
         */
        if($jwts->isvalid($jwtToken) && !$jwts->isExpired($jwtToken) && $jwts->checToken($jwtToken,$this->getParameter('app.jwtsecretkey')))
        {
            // On active l'utilisateur

            $payload = $jwts->getPayload($jwtToken);
            $user = $usersRepo->find($payload['user_id']);
            /**
             * On verifie si l'utilisateur existe
             * et si son compte n'a pas encore ete activer
             */
            if($user && !$user->getIsActived()){
                $user->setIsActived(true);
                $em->flush();
                $this->addFlash('success', 
                    'Votre compte a ete active avec success.
                    Connectez Vous pour acceder a votre compte'
                );
                return new Response($this->redirectToRoute('app_login'));

            }
        }

        /** 
         * Et s'il ya un probleme on ajoute un message
         * d'erreur et on redirige l'utilisateur
         * vers la page de connexion
         */
        $this->addFlash('danger', 
            'Le lien d\'activation est invalide ou a expirer.
            Un nouveau lien vous a ete envoye(e)
            Consulter le pour activer votre compte'
        );
        
        return new Response($this->redirectToRoute('app_main'));
    }
}
