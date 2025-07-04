<?php

namespace App\Http\Controllers\Auth;

use App\Models\Host;
use App\Models\Show;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $show = Show::create([
                'name' => 'Default Show',
                'description' => 'Default Show Description',
                'user_id' => $user->id,
            ]);

            $host = Host::create([
                'name' => $request->name,
                'user_id' => $user->id,
                'show_id' => $show->id,
            ]);

            $user->update(['current_host_id' => $host->id]);

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect(route('games.index', absolute: false));
    }
}
