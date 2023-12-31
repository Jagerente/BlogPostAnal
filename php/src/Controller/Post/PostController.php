<?php

namespace App\Controller\Post;

use App\Service\PostAnalyticsService;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Post;

use App\Enum\PostStatusEnum;
use App\Enum\RoleEnum;

use App\Form\Post\CreatePostType;
use App\Form\Post\EditPostType;
use App\Form\Post\ModeratePostType;

use App\Repository\PostRepository;

#[Route('/'), \Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted('IS_AUTHENTICATED_FULLY')]
class PostController extends AbstractController
{
    public function __construct(
        private Security $security,
        private PostAnalyticsService $analyticsService,
    ) {
    }

    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Security $security): Response
    {
        $role = $this->security->getuser() ? $this->security->getuser()->getRoles()[0] : RoleEnum::User;

        // Generate unique views for each role
        switch ($role) {
            case RoleEnum::Author:
                $userId = $this->security->getUser()->getId();
                $posts = $postRepository->findBy(
                    ['author' => $userId],
                    ['status' => 'ASC', 'created_at' => 'DESC']
                );

                $posts_declined = [];
                $posts_pending = [];
                $posts_approved = [];

                foreach ($posts as $post) {
                    switch ($post->getStatus()) {
                        case PostStatusEnum::Declined:
                            $posts_declined[] = $post;
                            break;
                        case PostStatusEnum::Pending:
                            $posts_pending[] = $post;
                            break;
                        case PostStatusEnum::Approved:
                            $posts_approved[] = $post;
                            break;
                        default:
                            break;
                    }
                }

                return $this->render('post/author/list/index.html.twig', [
                    'posts_declined' => $posts_declined,
                    'posts_pending' => $posts_pending,
                    'posts_approved' => $posts_approved,
                ]);
            case RoleEnum::Moderator:
                $posts = $postRepository->findBy([], ['created_at' => 'DESC']);
                $posts_declined = [];
                $posts_pending = [];
                $posts_approved = [];

                foreach ($posts as $post) {
                    switch ($post->getStatus()) {
                        case PostStatusEnum::Declined:
                            $posts_declined[] = $post;
                            break;
                        case PostStatusEnum::Pending:
                            $posts_pending[] = $post;
                            break;
                        case PostStatusEnum::Approved:
                            $posts_approved[] = $post;
                            break;
                    }
                }

                return $this->render('post/moderator/list/index.html.twig', [
                    'posts_declined' => $posts_declined,
                    'posts_pending' => $posts_pending,
                    'posts_approved' => $posts_approved,
                ]);

            default:
                $posts = $postRepository->findBy(['status' => PostStatusEnum::Approved], ['created_at' => 'DESC']);

                return $this->render('post/guest/list/index.html.twig', [
                    'posts' => $posts,
                ]);
        }
    }

    #[Route('/create', name: 'post_create', methods: ['GET', 'POST'])]
    public function create(Request $request, PostRepository $postRepository, Security $security): Response
    {
        // Author only access
        $this->denyAccessUnlessGranted(RoleEnum::Author);

        $user = $this->security->getuser();

        // Preconfigure post
        $post = new Post();
        $post->setAuthor($user)
            ->setStatus(PostStatusEnum::Pending);

        // Create form
        $form = $this->createForm(CreatePostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Add post to database
            $postRepository->save($post, true);

            $this->analyticsService->sendCreateEvent($user, $post);

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/author/create/index.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post, Security $security): Response
    {
        // For Guest only approved posts available to show
        if ($post->getStatus() !== PostStatusEnum::Approved) {
            if (!$this->isGranted(RoleEnum::Author) && !$this->isGranted(RoleEnum::Moderator)) {
                throw $this->createAccessDeniedException('Access denied.');
            }
        }

        $user = $this->security->getuser();

        // Make sure user has default role at least
        $role = $user ? $user->getRoles()[0] : RoleEnum::User;

        // Generate different views for each role
        $template = match ($role) {
            RoleEnum::Author => 'post/author/show/index.html.twig',
            RoleEnum::Moderator => 'post/moderator/show/index.html.twig',
            default => 'post/guest/show/index.html.twig',
        };

        return $this->render($template, [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository): Response
    {
        // Only authors can edit posts
        $this->denyAccessUnlessGranted(RoleEnum::Author);

        $user = $this->security->getuser();

        $oldPost = clone $post;

        // Preconfigure post
        $post->setStatus(PostStatusEnum::Pending);
        $post->setModeratorNote(null);

        $form = $this->createForm(EditPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Edit post in database
            $postRepository->save($post, true);

            $this->analyticsService->sendEditEvent($user, $oldPost, $post);

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/author/edit/index.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/moderate', name: 'post_moderate', methods: ['GET', 'POST'])]
    public function moderate(Request $request, Post $post, PostRepository $postRepository): Response
    {
        // Only moderator can moderate posts
        $this->denyAccessUnlessGranted(RoleEnum::Moderator);

        $user = $this->security->getuser();

        $oldPost = clone $post;

        $form = $this->createForm(ModeratePostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Remove moderator note if post is not declined
            if ($post->getStatus() != PostStatusEnum::Declined) {
                $post->setModeratorNote(null);
            }

            // Save in database
            $postRepository->save($post, true);

            $this->analyticsService->sendModerateEvent($user, $oldPost, $post);

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/moderator/moderate/index.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        // Only author can delete posts
        $this->denyAccessUnlessGranted(RoleEnum::Author);

        $user = $this->security->getuser();

        $oldPost = clone $post;

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);

            $this->analyticsService->sendDeleteEvent($user, $oldPost);
        }

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }
}