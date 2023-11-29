<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;

#[Route('/category', name:'category_')]

class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }
    #[Route('/{categoryName}', methods: ["GET"], requirements:['categoryName' => '\w+'], name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $categoryID = $categoryRepository->findOneBy(['name' => $categoryName]);
    
        if(!$categoryID) {
            throw $this->createNotFoundException(
                'Aucune catégorie nommée ' . $categoryName
            );
        } else {
            $programs = $programRepository->findBy(
                ['category' => $categoryID->getID()],
                ['id' => 'DESC']
            );
        }
        return $this->render('category/show.html.twig', ['programs' => $programs, "categoryName" => $categoryName]);
    }
}