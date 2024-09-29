<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
    #[Route('/show', name: 'app_author_show')]
    public function showAuthor():Response{
        $authorName='Victor Hugo';
        $authorEmail='vh@gmail.com';
          return $this->render('author/show.html.twig',
    array(
        'authorName'=>$authorName,
        'authorEmail'=>$authorEmail
    ));
    }

    #[Route('/list', name: 'app_list_authors')]
    public function listAuthors():Response{
        $authors=[
            ["id"=> 1 , "authorName" => "Victor Hugo", "picture" => "images/th.jpg" , "nbrBooks" => 100],
            ["id"=> 2 , "authorName" => "William S", "picture" => "images/ws.jpg" , "nbrBooks" => 200],
            ["id"=> 3 , "authorName" => "Taha Hsin", "picture" => "images/ths.jpg" , "nbrBooks" => 300],
        ];
        return $this->render('author/list.html.twig',
        array(
            "list"=>$authors
        ));
    }

    #[Route('/details/{id}', name: 'app_author_details')]
    public function authorDetails(int $id): Response
    {
        $authors=[
            ["id" => 1, "authorName" => "Victor Hugo", "picture" => "images/th.jpg", "email" => "victor.hugo@gmail.com", "nbrBooks" => 100],
            ["id" => 2, "authorName" => "William Shakespeare", "picture" => "images/ws.jpg", "email" => "william.shakespeare@gmail.com", "nbrBooks" => 200],
            ["id" => 3, "authorName" => "Taha Hussein", "picture" => "images/ths.jpg", "email" => "taha.hussein@gmail.com", "nbrBooks" => 300],
        ];

        //Recherche de l'auteur par ID
        $author = null;
        foreach ($authors as $a) {
            if ($a['id']==$id){
                $author = $a;
                break;
            }
        }

        if (!$author){
            throw $this->createNotFoundException("L'auteur avec l'ID $id n'existe pas");
        }        

        return $this->render('author/showAuthor.html.twig',[
             'authorName' => $author['authorName'],
             'authorPicture' => $author['picture'],
             'authorEmail' => $author['email'],
             'authorBooks' => $author['nbrBooks'],
        ]);
    
    }

}
