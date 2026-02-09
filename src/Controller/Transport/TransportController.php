<?php

namespace App\Controller\Transport;

use App\Entity\Transport;
use App\Form\Transport1Type;
use App\Repository\TransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/admin/transport')]
final class TransportController extends AbstractController
{
    #[Route(name: 'app_transport_index', methods: ['GET'])]
    public function index(Request $request, TransportRepository $transportRepository): Response
    {
        $type = $request->query->get('type');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $minCapacity = $request->query->get('minCapacity');
        $available = $request->query->get('available');

        $minPrice = $minPrice !== null && $minPrice !== '' ? (float) $minPrice : null;
        $maxPrice = $maxPrice !== null && $maxPrice !== '' ? (float) $maxPrice : null;
        $minCapacity = $minCapacity !== null && $minCapacity !== '' ? (int) $minCapacity : null;
        $available = ($available === '1' ? true : ($available === '0' ? false : null));

        $transports = $transportRepository->findByFilters($type, $minPrice, $maxPrice, $minCapacity, $available);

        return $this->render('TransportTemplate/transport/index.html.twig', [
            'transports' => $transports,
            'filters' => [
                'type' => $type,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'minCapacity' => $minCapacity,
                'available' => $available,
            ],
        ]);
    }

    #[Route('/new', name: 'app_transport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $transport = new Transport();
        $form = $this->createForm(Transport1Type::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires ou corriger les erreurs du formulaire.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move($this->getParameter('kernel.project_dir').'/public/uploads/transports', $newFilename);
                    $transport->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to upload image: '.$e->getMessage());
                }
            }

            $entityManager->persist($transport);
            $entityManager->flush();

            return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('TransportTemplate/transport/new.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_show', methods: ['GET'])]
    public function show(Transport $transport): Response
    {
        return $this->render('TransportTemplate/transport/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transport $transport, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(Transport1Type::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire avant de soumettre.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    // Delete old image if exists
                    if ($transport->getImage()) {
                        $oldImagePath = $this->getParameter('kernel.project_dir').'/public/uploads/transports/'.$transport->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $imageFile->move($this->getParameter('kernel.project_dir').'/public/uploads/transports', $newFilename);
                    $transport->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to upload image: '.$e->getMessage());
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('TransportTemplate/transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_delete', methods: ['POST'])]
    public function delete(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transport->getId(), $request->getPayload()->getString('_token'))) {
            // Delete associated image if exists
            if ($transport->getImage()) {
                $imagePath = $this->getParameter('kernel.project_dir').'/public/uploads/transports/'.$transport->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($transport);
            $entityManager->flush();
            
            $this->addFlash('success', 'Transport supprimé avec succès!');
        }

        return $this->redirectToRoute('app_transport_index', [], Response::HTTP_SEE_OTHER);
    }
}
