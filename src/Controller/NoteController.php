<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Service\MarkdownService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/note")
 */
class NoteController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var MarkdownService
     */
    private $markdown;

    public function __construct(EntityManagerInterface $em, MarkdownService $markdown)
    {
        $this->em = $em;
        $this->markdown = $markdown;
    }

    /**
     * @Route("/", name="note_index", methods={"GET"})
     */
    public function index(): Response
    {
        $notes = $this->em->getRepository(Note::class)->findBy([]);
        return $this->render('note/index.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/new", name="note_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($note);
            $this->em->flush();

            return $this->redirectToRoute("note_show", ["id" => $note->getId()]);
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="note_show", methods={"GET"})
     */
    public function show(Note $note): Response
    {
        $content = $this->markdown->parse($note->getContent());
        return $this->render('note/show.html.twig', [
            "note" => $note,
            "content" => $content
        ]);
    }

    /**
     * @Route("/{id}/edit", name="note_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="note_delete", methods={"DELETE"})
     * @param Request $request
     * @param Note $note
     * @return Response
     */
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid("delete" . $note->getId(), $request->query->get("_token"))) {
            $this->em->remove($note);
            $this->em->flush();
        }

        return $this->json(null, 204);
    }
}
