<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Employeur;
use App\Entity\Utilisateur;
use App\Form\CandidatFormType;
use App\Form\EmployeurFormType;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use App\Service\JWTTokenService;
use App\Service\SendMailService;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
class RegistrationController extends AbstractController
{
    
    #[Route('/inscription', name: 'app_register')]
    public function register(
        JWTTokenService $jwtTokenService , 
        SendMailService $mailService,Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        UsersAuthenticator $authenticator, 
        EntityManagerInterface $entityManager
    ): Response
    {

        /**
         * Verifier si c'est un client ou un candidat
         */
        
        $user = new Candidat(); 
        $form = $this->createForm(CandidatFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setGenre("F");
            //$user->setRoles(["ROLE_ADMIN"]);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            //on envoi le message
            $message = 'Activation de compte chez Feulefack Industrie';
            $this->_senTokenByMail($user, $message, $mailService, $jwtTokenService);
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
    #[Route('/inscription_em', name: 'register_em')]
    public function registerEmploye(
        JWTTokenService $jwtTokenService , 
        SendMailService $mailService,
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator, 
        UsersAuthenticator $authenticator, 
        EntityManagerInterface $entityManager): Response
    {

        $user = new Employeur();
        $form = $this->createForm(EmployeurFormType::class, $user);
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
            //on envoi le message
            $message = 'Activation de compte chez Feulefack Industrie';
            $this->_senTokenByMail($user, $message, $mailService, $jwtTokenService);
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        
        }
        return $this->render('registration/register_em.html.twig', [
            'registrationFormEm' => $form->createView(),
        ]);
    }

    public function _senTokenByMail(
        Utilisateur $user,
        String $message,
        SendMailService $mailService, 
        JWTTokenService $jwtTokenService
    ) : void 
    {
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
        $mailService->sendMail(
            'no-reply@feulefackindustrie.com',
            $user->getEmail(),
            $message,
            'newUser',
            compact('user','jwtToken')
        );
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
                    'Votre compte a ete active avec success.'
                );
                return new Response($this->redirectToRoute('app_card_index'));

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
