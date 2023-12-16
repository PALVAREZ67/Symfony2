<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/program', name:'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        if(!$session->has('total')) {
            $session->set('total', 0);
        }

        $total = $session->get('total');

        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }


    #[Route('/new', name: 'new')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManagerInterface, MailerInterface $mailer,
        SluggerInterface $slugger
    ): Response
    {

        $program = new Program();
        $test = [];
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManagerInterface->persist($program);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'The new program has been created');
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);

            return $this->redirectToRoute('program_index');
        }


        return $this->render('program/new.html.twig', ['form' => $form, "test" => $test]);
    }


    #[Route('/{programID}', methods: ["GET"], name: 'show' )]
    public function show(
        #[MapEntity(mapping: ['programID' => 'id'])] Program $program,
        ProgramDuration $programDuration
    ): Response
    {
        if(!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program->getId() . 'found in program\'s table'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'programDuration' => $programDuration->calculate($program)
        ]);
    }

    #[Route('/{programID}/season/{seasonID}', methods: ["GET"], requirements:['seasonID' => '\d+'], name: 'season_show' )]        
    public function showSeason(
        #[MapEntity(mapping: ['programID' => "id"])] Program $program,
        #[MapEntity(mapping: ['seasonID' => "id"])] Season $season
    ): Response
    {
        return $this->render('program/season_show.html.twig', ['season' => $season, "program" => $program]);
    }

    #[Route('/{programID}/season/{seasonID}/episode/{episodeID}', methods: ["GET"], requirements:['seasonID' => '\d+'], name: 'episode_show' )]
    public function showEpisode(
        #[MapEntity(mapping: ['programID' => "id"])] Program $program,
        #[MapEntity(mapping: ['seasonID' => "id"])] Season $season,
        #[MapEntity(mapping: ['episodeID' => "id"])] Episode $episode
    ): Response
    {
        return $this->render('program/episode_show.html.twig', ['season' => $season, "program" => $program, "episode" => $episode]);
    }
}
