<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;
use App\Http\Requests\DestroyHostRequest;

class DestroyHostController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DestroyHostRequest $request, Host $host)
    {
        $host->delete();

        return redirect()->back()->with('success', 'Host deleted');
    }
}
