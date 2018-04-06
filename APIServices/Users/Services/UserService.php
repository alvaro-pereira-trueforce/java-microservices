<?php

namespace APIServices\Users\Services;

use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use APIServices\Users\Exceptions\UserNotFoundException;
use APIServices\Users\Events\UserWasCreated;
use APIServices\Users\Events\UserWasDeleted;
use APIServices\Users\Events\UserWasUpdated;
use APIServices\Users\Repositories\UserRepository;

class UserService
{
    private $database;

    private $dispatcher;

    private $userRepository;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        UserRepository $userRepository
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
    }

    public function getAll($options = [])
    {
        return $this->userRepository->get($options);
    }

    public function getById($userId, array $options = [])
    {
        $user = $this->getRequestedUser($userId);

        return $user;
    }

    public function create($data)
    {
        $user = $this->userRepository->create($data);

        $this->dispatcher->fire(new UserWasCreated($user));

        return $user;
    }

    public function update($userId, array $data)
    {
        $user = $this->getRequestedUser($userId);

        $this->userRepository->update($user, $data);

        $this->dispatcher->fire(new UserWasUpdated($user));

        return $user;
    }

    public function delete($userId)
    {
        $user = $this->getRequestedUser($userId);

        $this->userRepository->delete($userId);

        $this->dispatcher->fire(new UserWasDeleted($user));
    }

    private function getRequestedUser($userId)
    {
        $user = $this->userRepository->getById($userId);

        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
