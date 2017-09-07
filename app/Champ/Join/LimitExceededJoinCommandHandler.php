<?php

namespace Champ\Join;

use Champ\Account\Repositories\UserRepository;
use Champ\Account\User;
use Champ\Championship\Repositories\ChampionshipRepository;
use Champ\Join\Repositories\WaitingListRepositoryInterface;
use Champ\Services\RegisterUser;
use Laracasts\Commander\CommandHandler;
use Laracasts\Commander\Events\DispatchableTrait;

class LimitExceededJoinCommandHandler implements CommandHandler
{
    /**
     * Championship Repository.
     *
     * @var Champ\Championship\Repositories\ChampionshipRepository
     */
    protected $championshipRepository;

    /**
     * Waiting List Repository.
     *
     * @var Champ\Join\Repositories\WaitingListRepositoryInterface
     */
    protected $waitingList;

    /**
     * Register User Service.
     *
     * @var Champ\Services\RegisterUser
     */
    private $userService;

    use DispatchableTrait;

    /**
     * Constructor.
     *
     * @param ChampionshipRepository $championshipRepository
     * @param UserRepository         $userRepository
     */
    public function __construct(
        ChampionshipRepository $championshipRepository,
        WaitingListRepositoryInterface $waitingList,
        RegisterUser $userService
    ) {
        $this->championshipRepository = $championshipRepository;
        $this->waitingList = $waitingList;
        $this->userService = $userService;
    }

    public function handle($command)
    {
        $user = $this->userService->getOrCreateUser($command);
        $championship = $this->championshipRepository->find($command->championship_id);
        $waitingList = WaitingList::register($user->id, $championship->id, $command->competitions[0]);

        $this->waitingList->save($waitingList);

        $this->dispatchEventsFor($user);

        return $waitingList;
    }
}
