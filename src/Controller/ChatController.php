<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Repository\ChatRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="chat")
     */
    public function index():Response
    {
        return $this->render('chat/index.html.twig');
    }

    /**
     * @param ChatRepository $repository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/show", name="show")
     */
    public function show(ChatRepository $repository){

        $messages = $repository->findLatest();
        return $this->json([
            'code' => 200,
            $messages => $messages
        ], 200);
    }

    /**
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/create", name="create")
     */
    public function create(Request $request, ObjectManager $manager){

        if($request->request->count() > 0){

            $chat = new Chat();
            $chat->setMessage($request->request->get('message'))
                ->setCreatedAt(new \DateTime());

            $errors = $this->get('Validator')->validate($chat);
            if(count($errors) === 0){

                $manager->persist($chat);
                $manager->flush();

            }else{

                return $this->json([
                    'code' => 301,
                    'errors' => $errors
                ]);
            }

        }
    }
}
