<?php

namespace App\Http\Controllers\Admin;

use App\Models\Scope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use yajra\Datatables\Datatables;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Notifications\PasswordResetMail;

class UserController extends Controller
{
    /**
     * User Repository
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * User Controller Constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('auth');
    }

    /**
     * Users list view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.user.index');
    }

    /**
     * Users Datatables Data
     *
     * @return \yajra\Datatables\Datatables
     */
    public function getUsersData()
    {
        return Datatables::of($this->userRepository->getAll())
            ->editColumn('status', function ($user) {
                return $user->status ? 'Active' : 'Inactive';
            })
            ->addColumn('action', function ($user) {
                return $this->getButtons($user);
            })
            ->make(true);
    }

    private function getButtons($user)
    {
        $edit = sprintf(
            '<a href="%s" class="btn btn-xs btn-primary edit">%s</a>',
            route('admin.user.edit', $user->id),
            'Edit'
        );
        $scope = sprintf(
            '<a href="%s" class="btn btn-xs btn-success edit">%s</a>',
            route('admin.user.scope.add', $user->id),
            'Scope'
        );

        return $edit . ' ' . $scope;
    }

    /**
     * User create form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store user info.
     *
     * @param  StoreUserRequest $request
     *
     * @return mixed
     */
    protected function store(StoreUserRequest $request)
    {
        $request['user_type'] = 'CLIENT';
        $user = $this->userRepository->save($request);
        $user->assignRole([2]); // Giving idp-client role

        try {
            $user->notify(new PasswordResetMail($user));
        } catch (\Exception $exception) {
            Log::error('Code: '.$exception->getCode() . ' Message: '.$exception->getMessage());
        }

        return redirect()->route('admin.user.index')->withFlashSuccess(trans('User created successfully.'));
    }

    /**
     * Update user form.
     *
     * @param  App\Models\User $user
     *
     * @return mixed
     */
    protected function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update user.
     *
     * @param  App\Models\User $user
     *
     * @param UpdateUserRequest $request
     *
     * @return mixed
     */
    protected function update(User $user, UpdateUserRequest $request)
    {
        $user = $this->userRepository->update($user, $request);

        return redirect()->route('admin.user.index')->withFlashSuccess(trans('User updated successfully.'));
    }

    public function addScope(User $user)
    {
        $scopes = Scope::all();

        $userScopeIds = [];

        $userScopes = $user->clientScopes;

        foreach ($userScopes as $scope) {
            array_push($userScopeIds, $scope->id);
        }

        return view('admin.user.add-scope', compact('scopes', 'user', 'userScopeIds'));

    }

    public function saveScope(Request $request, User $user)
    {
        $user->clientScopes()->sync($request['scopes']);
        return redirect()->route('admin.user.index')->withFlashSuccess(trans('Scope updated successfully'));

    }
}
