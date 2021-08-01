<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $html = $request->input('html');

        $data = Browsershot::html($html)
             ->addChromiumArguments([
                 'no-sandbox',
                 'disable-setuid-sandbox'
             ])
             ->pdf();

        return response()->streamDownload(function () use ($data) {
            echo $data;
        }, 'browsershot-'.date('YmdHis').'.pdf');
    }
}
