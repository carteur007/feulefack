<?php

namespace App\Controller;

use App\Entity\PackCV;
use App\Repository\PackCVRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card', name: 'app_card_')]
class CardSessionController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        EntityManagerInterface $manager,
        PackCVRepository $packcvrepo,
        UtilisateurRePository $usersrepo,
        SessionInterface $session
    ): Response
    {   
        $panier = $session->get("panier",[]);
        $datapanier = [];
        $total = 0;
        foreach ($panier as $id => $quantite) {
            $packcv = $packcvrepo->find($id);
            $datapanier[] = [
                "packcv" => $packcv,
                "quantite" => $quantite
            ];
            $total += $packcv->getPrix()*$quantite;
        }

        return $this->render('card_session/index.html.twig', [
            compact("datapanier","total")
        ]);
    }
    #[Route('/shop', name: 'shop')]
    public function index_card(
        PackCVRepository $packcvrepo,
        UtilisateurRePository $usersrepo,
        SessionInterface $session
    ): Response
    {   
        $panier = $session->get("panier",[]);
        $datapanier = [];
        $total = 0;
        foreach ($panier as $id => $quantite) {
            $packcv = $packcvrepo->find($id);
            $datapanier[] = [
                "packcv" => $packcv,
                "quantite" => $quantite
            ];
            $total += $packcv->getPrix()*$quantite;
        }

        return $this->render('card_session/card.html.twig', [
            compact("datapanier","total")
        ]);
    }
    
    #[Route('/add/{id}', name: 'add')]
    public function add(SessionInterface $session, PackCV $pack,UtilisateurRepository $usersrepo){
        $user = null;
        $id = $pack->getId();
        $panier = $session->get("panier",[]);
        $username = $session->get("_security.last_username");
        
        if (!$username) {
            //If user is guest no connected
        }else {
            $user = $usersrepo->findOneBy(["email"=>$username]);
            //on le rappel d'activer son compte s'il n'est pas encore activer
            //en ajoutant un message flache
            //on ajoute ses pack sil passe la commande avec un status:encour de validation
            //et s'l procede au payement on modifie le status en:ok et on l'affiche
        }


        if (!empty($panier[$id])) {
            $panier[$id]++;
        }else {
            $panier[$id] = 1;
        }
        $session->set("panier",$panier);


        return $this->redirectToRoute("app_card_index");
    }
}
