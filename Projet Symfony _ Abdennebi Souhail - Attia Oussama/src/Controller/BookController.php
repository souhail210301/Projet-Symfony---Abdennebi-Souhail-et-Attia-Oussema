<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book")
     */
    public function index(): Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/book/new", name="book_create")
     * Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('book_list');
        }
        return $this->render('book/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/book/{id}", name="book_show")
     */
    public function show($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)
            ->find($id);
        return $this->render('book/show.html.twig',
            array('book' => $book));
    }

    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/book/edit/{id}", name="book_edit")
     * Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, $id)
    {

        $book = new Book();
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('book_list');
        }
        return $this->render('book/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/book/delete/{id}",name="book_delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('book_list');
    }
}
