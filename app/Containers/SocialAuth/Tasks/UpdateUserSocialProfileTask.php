<?php

namespace App\Containers\SocialAuth\Tasks;

use App\Containers\User\Data\Repositories\UserRepository;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Tasks\Task;

/**
 * Class UpdateUserSocialProfileTask.
 *
 * @author Mahmoud Zalt <mahmoud@zalt.me>
 */
class UpdateUserSocialProfileTask extends Task
{

    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param      $userId
     * @param null   $token
     * @param null   $expiresIn
     * @param null   $refreshToken
     * @param null   $tokenSecret
     * @param null   $provider
     * @param null   $avatar
     * @param null   $avatarOriginal
     * @param null   $socialId
     * @param null   $nickname
     * @param null   $name
     * @param null   $email
     *
     * @return  mixed
     * @throws  UpdateResourceFailedException
     */
    public function run(
        $userId,
        $token = null,
        $expiresIn = null,
        $refreshToken = null,
        $tokenSecret = null,
        $avatar = null,
        $avatarOriginal = null,
        $provider = null,
        $socialId = null,
        $nickname = null,
        $name = null,
        $email = null
    ) {
        $attributes = [];

        if ($token) {
            $attributes['social_token'] = $token;
        }

        if ($expiresIn) {
            $attributes['social_expires_in'] = $expiresIn;
        }

        if ($refreshToken) {
            $attributes['social_refresh_token'] = $refreshToken;
        }

        if ($tokenSecret) {
            $attributes['social_token_secret'] = $tokenSecret;
        }

        if ($avatar) {
            $attributes['social_avatar'] = $avatar;
        }

        if ($avatarOriginal) {
            $attributes['social_avatar_original'] = $avatarOriginal;
        }

        $attributes += $this->parseParams($provider, $socialId, $nickname, $name, $email);

        // check if data is empty
        if (empty($attributes)) {
            throw new UpdateResourceFailedException('Inputs are empty.');
        }

        // updating the attributes
        $user = $this->repository->update($attributes, $userId);

        return $user;
    }

    /**
     * @param null $provider
     * @param null $socialId
     * @param null $nickname
     * @param null $name
     * @param null $email
     *
     * @return  mixed
     */
    private function parseParams(
        $provider = null,
        $socialId = null,
        $nickname = null,
        $name = null,
        $email = null
    ) {
        $attributes = [];

        if ($provider) {
            $attributes['social_provider'] = $provider;
        }

        if ($socialId) {
            $attributes['social_id'] = $socialId;
        }

        if ($nickname) {
            $attributes['social_nickname'] = $nickname;
        }

        if ($name) {
            $attributes['name'] = $name;
        }

        if ($email) {
            $attributes['email'] = $email;
        }
        return $attributes;
    }
}
