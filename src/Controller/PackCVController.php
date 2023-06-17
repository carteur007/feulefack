<?php

namespace App\Controller;

use App\Entity\PackCV;
use App\Form\PackCVType;
use App\Repository\PackCVRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pack/c/v', name: 'app_pack_c_v_')]
class PackCVController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PackCVRepository $packCVRepository): Response
    {
        return $this->render('pack_cv/index.html.twig', [
            'pack_c_vs' => $packCVRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, PackCVRepository $packCVRepository): Response
    {
        $packCV = new PackCV();
        $form = $this->createForm(PackCVType::class, $packCV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $packCVRepository->save($packCV, true);

            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pack_cv/new.html.twig', [
            'pack_c_v' => $packCV,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PackCV $packCV): Response
    {
        return $this->render('pack_cv/show.html.twig', [
            'pack_c_v' => $packCV,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PackCV $packCV, PackCVRepository $packCVRepository): Response
    {
        $form = $this->createForm(PackCVType::class, $packCV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $packCVRepository->save($packCV, true);

            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pack_cv/edit.html.twig', [
            'pack_c_v' => $packCV,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PackCV $packCV, PackCVRepository $packCVRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$packCV->getId(), $request->request->get('_token'))) {
            $packCVRepository->remove($packCV, true);
        }

        return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
    }
}
