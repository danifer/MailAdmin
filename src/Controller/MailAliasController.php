<?php

namespace App\Controller;

use App\Entity\MailAlias;
use App\Form\MailAliasType;
use App\Repository\MailAliasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mail/alias')]
final class MailAliasController extends AbstractController
{
    #[Route(name: 'app_mail_alias_index', methods: ['GET'])]
    public function index(MailAliasRepository $mailAliasRepository): Response
    {
        return $this->render('mail_alias/index.html.twig', [
            'mail_aliases' => $mailAliasRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mail_alias_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mailAlias = new MailAlias();
        $form = $this->createForm(MailAliasType::class, $mailAlias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mailAlias);
            $entityManager->flush();

            $this->addFlash('success', 'Mail alias created successfully.');
            return $this->redirectToRoute('app_mail_alias_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mail_alias/new.html.twig', [
            'mail_alias' => $mailAlias,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mail_alias_show', methods: ['GET'])]
    public function show(MailAlias $mailAlias): Response
    {
        return $this->render('mail_alias/show.html.twig', [
            'mail_alias' => $mailAlias,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mail_alias_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MailAlias $mailAlias, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MailAliasType::class, $mailAlias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Mail alias updated successfully.');
            return $this->redirectToRoute('app_mail_alias_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mail_alias/edit.html.twig', [
            'mail_alias' => $mailAlias,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mail_alias_delete', methods: ['POST'])]
    public function delete(Request $request, MailAlias $mailAlias, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mailAlias->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mailAlias);
            $entityManager->flush();
            $this->addFlash('success', 'Mail alias deleted successfully.');
        }

        return $this->redirectToRoute('app_mail_alias_index', [], Response::HTTP_SEE_OTHER);
    }
}
