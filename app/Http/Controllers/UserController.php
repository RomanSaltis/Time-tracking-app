<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{

    /**
     * Validate the user data.
     *
     * @param  Request  $request
     * @return array
     */
    protected function validateUserData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|min:2|string',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
        ]);
    }

    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $validatedData = $this->validateUserData($request);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        auth()->login($user);

        return redirect('/tasks');
    }

    /**
     * Log in a user.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($validatedData)) {
            return redirect('/tasks');
        } else {
            return redirect()->back()->withErrors(['message' => 'Invalid credentials']);
        }
    }

    /**
     * Log out the authenticated user.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        auth()->logout();
        return redirect('/login');
    }

    /**
     * Display the user registration form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Display the user login form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Display the list of users.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function index(User $user): View
    {
        $this->authorize('viewAny', $user);
        if (!is_superadmin(user())){
            $users = User::where('id', user()->id)->get();
        }else{
            $users = User::all();
        }
        return view('users.index', compact('users'));
    }

    /**
     * Display the user creation form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateUserData($request);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the specified user.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    /**
     * Display the user edit form.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Delete the specified user.
     *
     * @param  User  $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
