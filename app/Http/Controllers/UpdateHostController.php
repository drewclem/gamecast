<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\Show;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateHostRequest;

class UpdateHostController extends Controller
{
  public function __invoke(UpdateHostRequest $request, Show $show, Host $host): RedirectResponse
  {
    $data = $request->validated();

    if ($request->hasFile('avatar')) {
      // Delete old avatar if it exists
      if ($host->avatar) {
        Storage::disk('public')->delete($host->avatar);
      }

      $file = $request->file('avatar');
      $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
      $path = $file->storeAs('avatars', $filename, 'public');

      $host->update([
        'avatar' => $path,
      ]);
    }

    $host->update([
      'name' => $data['name'],
      'color' => $data['color'],
    ]);

    return redirect()->back()->with('success', 'Host updated successfully');
  }
}
