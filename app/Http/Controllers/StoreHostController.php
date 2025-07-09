<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\User;
use App\Mail\HostInvite;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreHostRequest;

class StoreHostController extends Controller
{
  public function __invoke(StoreHostRequest $request, Show $show): RedirectResponse
  {
    $data = $request->validated();

    $host = DB::transaction(function () use ($data, $show, $request) {
      $randomPassword = Str::random(10);

      $user = User::updateOrCreate([
        'email' => $data['email'],
      ], [
        'name' => $data['name'],
        'password' => Hash::make($randomPassword),
      ]);

      $data['user_id'] = $user->id;
      $host = $show->hosts()->create($data);

      $user->update(['current_host_id' => $host->id]);

      $invitation = Invitation::create([
        'host_id' => $host->id,
        'token' => Str::random(32),
        'expires_at' => now()->addDays(7),
      ]);

      if ($request->hasFile('avatar') && $host) {
        $file = $request->file('avatar');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        $host->update(['avatar' => $path]);
      }

      Mail::to($user->email)->send(new HostInvite($user->currentHost, $invitation));

      return $host;
    });

    return redirect()->back()->with('success', 'Host created successfully');
  }
}
