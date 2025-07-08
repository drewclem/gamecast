<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Invitation;
use Illuminate\Http\Request;

class ShowHostInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect()->route('home')->with('error', 'Invalid invitation token.');
        }

        $invitation->loadMissing('host.user');

        return Inertia::render('Auth/AcceptInvite', [
            'invitation' => $invitation,
        ]);
    }
}
