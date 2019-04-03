<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
// use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/micro-post")
 */
class MicroPostController
{
  /** @var \Twig_Environment */
  private $twig;

  /** @var MicroPostRepository */
  private $microPostRepository;

  /** @var FormFactoryInterface */
  private $formFactory;

  /** @var EntityManagerInterface */
  private $entityManager;

  /** @var RouterInterface */
  private $router;

  /** @var FlashBagInterface */
  private $flashBag;

  /** @var AuthorizationCheckerInterface */
  private $authorizationChecker;

  /**
   * @param Twig_Environment              $twig                 [description]
   * @param MicroPostRepository           $microPostRepository  [description]
   * @param FormFactoryInterface          $formFactory          [description]
   * @param EntityManagerInterface        $entityManager        [description]
   * @param RouterInterface               $router               [description]
   * @param FlashBagInterface             $flashBag             [description]
   * @param AuthorizationCheckerInterface $authorizationChecker [description]
   */
  function __construct(
    \Twig_Environment $twig,
    MicroPostRepository $microPostRepository,
    FormFactoryInterface $formFactory,
    EntityManagerInterface $entityManager,
    RouterInterface $router,
    FlashBagInterface $flashBag,
    AuthorizationCheckerInterface $authorizationChecker)
  {
    $this->twig = $twig;
    $this->microPostRepository = $microPostRepository;
    $this->formFactory = $formFactory;
    $this->entityManager = $entityManager;
    $this->router = $router;
    $this->flashBag = $flashBag;
    $this->authorizationChecker = $authorizationChecker;
  }
  /**
   * @Route("/", name="micro_post_index")
   */
  public function index()
  {
    $html = $this->twig->render(
      'micro-post/index.html.twig', [
        'posts' =>
        $this->microPostRepository->findBy([], ['time' => 'DESC'])
      // $this->microPostRepository->findAll()
      ]
    );

    return new Response($html);
  }

  /**
   * @Route("/edit/{id}", name="micro_post_edit")
   * @Security("is_granted('edit', microPost)", message="Access id deny")
   */
  public function edit(MicroPost $microPost, Request $request)
  {
    // $this->denyUnlessGranted('edit', $microPost);
    //
    // if(!$this->authorizationChecker->isGranted('edit', $microPost)){
    //   throw new UnauthorizedHttpException();
    // }
    //
    $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost
        );

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
      $this->entityManager->flush();

      return new RedirectResponse(
        $this->router->generate('micro_post_index')
      );
    }

    return new Response($this->twig->render(
      'micro-post/add.html.twig',
      ['form' => $form->createView()]
    ));
  }

  /**
   * @Route("/delete/{id}", name="micro_post_delete")
   * @Security("is_granted('delete', microPost)", message="Access id deny")
   */
  public function delete(MicroPost $microPost)
  {
    $this->entityManager->remove($microPost);
    $this->entityManager->flush();

    $this->flashBag->add('notice', 'Micro post was deleted');

    return new RedirectResponse(
      $this->router->generate('micro_post_index')
    );

  }

  /**
   * @Route ("/add", name="micro_post_add")
   */
  public function add(Request $request)
  {
    $microPost = new MicroPost();
    $microPost->setTime(new \DateTime());

    $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost
        );
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
      $this->entityManager->persist($microPost);
      $this->entityManager->flush();

      return new RedirectResponse(
        $this->router->generate('micro_post_index')
      );
    }

    return new Response($this->twig->render(
      'micro-post/add.html.twig',
      ['form' => $form->createView()]
    ));
  }

  /**
   * @Route("/{id}", name="micro_post_post")
   */
  public function post(MicroPost $microPost)
  {

    return new Response(
      $this->twig->render(
        'micro-post/post.html.twig',
        [
          'post' => $microPost
        ]
      )
    );
  }
}
