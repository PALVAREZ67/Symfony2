<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/category', name:'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('Category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', ['form' => $form]);
    }

    #[Route('/{categoryName}', name: 'show')]
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
        dump($programs);
        return $this->render("Category/show.html.twig", ['programs' => $programs, "categoryName" => $categoryName]);
    }

}
