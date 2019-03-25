<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MicroPostRepository;

/**
 * @Route("/micro-post")
 */
class MicroPostController
{
  /**
   * @var \Twig_Environment
   */
  private $twig;
  /**
   * @var MicroPostRepository
   */
  private $microPostRepository;

  function __construct(
    \Twig_Environment $twig,
    MicroPostRepository $microPostRepository)
  {
    $this->twig = $twig;
    $this->microPostRepository = $microPostRepository;
  }
  /**
   * @Route("/", name="micro_post_index")
   */
  public function index()
  {
    $html = $this->twig->render('micro-post/index.html.twig', [
      'posts' => $this->microPostRepository->findAll()
    ]);

    return new Response($html);
  }
}
