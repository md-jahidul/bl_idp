<?php
namespace App\Services;

use Exception;
use App\Repositories\UserRepository;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends ApiBaseService
{

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserService constructor.
     * 
     * @param UserRepository $UserRepository
     */
    public function __construct(UserRepository $UserRepository)
    {
        $this->userRepository = $UserRepository;
    }

    /**
     * Store user info
     *
     * @return mixed|string
     */
    public function store(CreateUserRequest $request)
    {
        try {
            $data = $this->userRepository->store($request);
            return $this->sendSuccessResponse($data, 'User Device Token Saved');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], $exception->getStatusCode());
        }
    }

}