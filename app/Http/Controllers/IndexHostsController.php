<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class IndexHostsController extends Controller
{
    public function __invoke(Request $request)
    {
        $show = $request->user()->currentHost->show;
        $show->load('hosts.user');

        return Inertia::render('Hosts/IndexHosts', ['hosts' => $show->hosts]);
    }
}
