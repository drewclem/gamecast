<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AcceptHostInviteRequest;

class AcceptHostInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AcceptHostInviteRequest $request, Invitation $invitation)
    {
        DB::transaction(function () use ($invitation, $request) {
            $user = $invitation->host->user;

            $user->update([
                'password' => Hash::make($request->password),
                'email_verified_at' => now()
            ]);

            $invitation->update(['accepted_at' => now()]);

            Auth::login($user);

            return redirect()->route('dashboard');
        });
    }
}
