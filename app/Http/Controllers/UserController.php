<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderByType = $request->orderByType;
        $orderBy = $request->orderBy;
        $perPage = $request->perPage ?: $this->itemPerPage;

        $users = User::clients()
        ->when($orderBy != null, function ($query) use ($orderBy, $orderByType) {
            $query->orderBy($orderBy, $orderByType);
        })
        ->when($orderBy == null, function ($query) {
            $query->latest();
        })
        ->paginate($perPage)->onEachSide(1)->withQueryString();

        if ($users->currentPage() > $users->lastPage()) {
            $page = $users->lastPage();
            return redirect()->route('users.index', compact('page', 'perPage'));
        }
        return Inertia::render('Users/Index', compact('users', 'orderBy', 'orderBy'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $userInfo = array_merge($request->validated(), ['password' => Hash::make(12345678)]);
        User::create($userInfo);

        return back()->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if ($user->role === User::ROLE['admin']) {
            return back()->with('error', 'You cannot edit the admin user.');
        }
        if ($this->previous_route() !== 'users.edit') {
            session()->put('prevUrl', url()->previous());
        }

        return Inertia::render('Users/Edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($user->role === User::ROLE['admin']) {
            return back()->with('error', 'You cannot edit the admin user.');
        }

        $user->update($request->validated());

        $prevUrl = session('prevUrl');
        session()->pull('prevUrl');
        return redirect($prevUrl)->with('success', 'User updated successfully.');
    }

    /**
     * Remove selected resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selectedDelete(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'exists:users,id', Rule::notIn(User::ROLE['admin'])]
        ]);

        $user = User::whereIn('id', $request->ids)->delete();

        activity('User')
            ->performedOn(new User())
            ->withProperties(['ids' => $request->ids])
            ->event('deleted')
            ->log($user . ' items has been deleted.');

        return back()->with('message', 'User deleted successfully.');
    }
}
