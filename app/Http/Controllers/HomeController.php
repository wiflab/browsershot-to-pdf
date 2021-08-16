<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $data = $request->input('data');

        $merger = new Merger;

        foreach ($data as $content) {
            $rawPdf = $type === 'html' ? Browsershot::html($content)->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox'
            ])->pdf() : Browsershot::url($content)->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox'
            ])->pdf();

            $merger->addRaw($rawPdf);
        }

        $createdPdf = $merger->merge();

        return response()->streamDownload(function () use ($createdPdf) {
            echo $createdPdf;
        }, 'browsershot-'.date('YmdHis').'.pdf');
    }

    public function redirect()
    {
        return redirect(config('app.home_redirect'));
    }
}
