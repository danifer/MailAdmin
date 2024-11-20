<?php

namespace App\Controller;

use App\Entity\Domain;
use App\Form\DomainType;
use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/domain')]
final class DomainController extends AbstractController
{
    #[Route(name: 'app_domain_index', methods: ['GET'])]
    public function index(DomainRepository $domainRepository): Response
    {
        return $this->render('domain/index.html.twig', [
            'domains' => $domainRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_domain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $domain = new Domain();
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($domain);
            $entityManager->flush();

            $this->addFlash('success', 'Domain created successfully.');
            return $this->redirectToRoute('app_domain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('domain/new.html.twig', [
            'domain' => $domain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_domain_show', methods: ['GET'])]
    public function show(Domain $domain): Response
    {
        return $this->render('domain/show.html.twig', [
            'domain' => $domain,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_domain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Domain $domain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Domain updated successfully.');
            return $this->redirectToRoute('app_domain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('domain/edit.html.twig', [
            'domain' => $domain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_domain_delete', methods: ['POST'])]
    public function delete(Request $request, Domain $domain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domain->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($domain);
            $entityManager->flush();
            $this->addFlash('success', 'Domain deleted successfully.');
        }

        return $this->redirectToRoute('app_domain_index', [], Response::HTTP_SEE_OTHER);
    }
}
