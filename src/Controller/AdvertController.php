<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

#[Route('/admin/advert')]
class AdvertController extends AbstractController
{
    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(Request $request, AdvertRepository $advertRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $advertRepository->getAdvertPaginator($offset);

        return $this->render('advert/index.html.twig', [
            'adverts' => $paginator,
            'previous' => $offset - AdvertRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AdvertRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_advert_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

/*    #[Route('/{id}/edit', name: 'app_advert_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_advert_delete', methods: ['POST'])]
    public function delete(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }*/

#[Route('/{id}/publish', name: 'app_advert_publish', methods: ['GET', 'POST'])]
    public function publish(Advert $advert,Registry $workflows, EntityManagerInterface $entityManager): Response
    {
        $workflow = $workflows->get($advert);
        $workflow->apply($advert, 'publish');
        $entityManager->flush();

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_advert_reject', methods: ['GET', 'POST'])]
    public function reject(Advert $advert,Registry $workflows, EntityManagerInterface $entityManager): Response
    {
        $workflow = $workflows->get($advert);
        $workflow->apply($advert, 'reject');
        $entityManager->flush();

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

}
