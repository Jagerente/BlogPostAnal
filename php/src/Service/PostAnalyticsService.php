<?php

namespace App\Service;

use App\Entity\ISerializable;
use App\Entity\Post;
use App\Entity\PostAnalytics;
use App\Enum\PostEventsEnum;

final class PostAnalyticsService extends AnalyticsService
{
    public function send(ISerializable $data): void
    {
        $this->logger->debug('sending post analytics data: ' . $data->getSerialized());

        try {
            $response = $this->httpClient->request('POST', '/api/posts/', [
                'json' => $data->getValuesArray(),
            ]);

            $this->logger->debug('response: ' . $response->getContent());

        } catch (\Throwable $e) {
            $this->logger->error('Error during analytics event dispatch: ' . $e);
        }
    }

    public function sendModerateEvent(
        \Symfony\Component\Security\Core\User\UserInterface $user,
        Post $oldPost,
        Post $post
    ) {
        // Configure analytics message
        $analyticsData = new PostAnalytics();

        $analyticsData->setUserId($post->getAuthor()->getId());
        $analyticsData->setPostId($post->getId());
        $analyticsData->setEvent(PostEventsEnum::Moderated);
        $analyticsData->setDetails(
            $user->getRoles()[0] .
            " with id " .
            $user->getId() .
            " moderated post with id " .
            $post->getId() .
            ". Status changed from " .
            $oldPost->getStatus() .
            " to " .
            $post->getStatus() .
            ($post->getModeratorNote() ?
                ". Moderator note: \"" .
                $post->getModeratorNote() .
                "\"." :
                ".")
        );

        // Send analytics message
        $this->send($analyticsData);
    }

    public function sendDeleteEvent(
        \Symfony\Component\Security\Core\User\UserInterface $user,
        Post $post,
    ) {
        // Configure analytics message
        $analyticsData = new PostAnalytics();

        $analyticsData->setUserId($user->getId());
        $analyticsData->setPostId($post->getId());
        $analyticsData->setEvent(PostEventsEnum::Deleted);
        $analyticsData->setDetails(
            $user->getRoles()[0] .
            " with id " .
            $user->getId() .
            " deleted post with id " .
            $post->getId()
        );

        // Send analytics message
        $this->send($analyticsData);
    }

    public function sendEditEvent(
        \Symfony\Component\Security\Core\User\UserInterface $user,
        Post $oldPost,
        Post $post
    ) {
        // Configure analytics message
        $analyticsData = new PostAnalytics();
        $analyticsData->setUserId($user->getId());
        $analyticsData->setPostId($post->getId());
        $analyticsData->setEvent(PostEventsEnum::Edited);
        $analyticsData->setDetails(
            $user->getRoles()[0] .
            " with id " .
            $user->getId() .
            " edited post with id " .
            $post->getId() .
            " . Title changed from \"" .
            $oldPost->getTitle() .
            "\" to \"" .
            $post->getTitle() .
            "\"; Body changed from \"" .
            $oldPost->getBody() .
            "\" to \"" .
            $post->getBody() .
            "\"."
        );

        // Send analytics message
        $this->send($analyticsData);
    }

    public function sendCreateEvent(
        \Symfony\Component\Security\Core\User\UserInterface $user,
        Post $post,
    ) {
        // Configure analytics message
        $analyticsData = new PostAnalytics();
        $analyticsData->setUserId($post->getAuthor()->getId());
        $analyticsData->setPostId($post->getId());
        $analyticsData->setEvent(PostEventsEnum::Created);
        $analyticsData->setDetails(
            $user->getRoles()[0] .
            " with id " .
            $user->getId() .
            " created post with id " .
            $post->getId() .
            " . Title: \"" .
            $post->getTitle() .
            "\"; Body: \"" .
            $post->getBody() .
            "\"."
        );

        // Send analytics message
        $this->send($analyticsData);
    }
}