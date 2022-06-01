<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="book_list")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $propertySearch->getName();
            if ($nom != "")
                $books = $this->getDoctrine()->getRepository(Book::class)->findBy(['name' => $nom]);

        }

        return $this->render('book/index.html.twig', [
            'form' =>$form->createView(),
            'books' => $books,
        ]);
    }

}
