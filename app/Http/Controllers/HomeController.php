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
        $html = $request->input('html');

        $merger = new Merger;
        foreach ($html as $content) {
            $data = Browsershot::html($content)
            ->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox'
            ])
            ->pdf();

            $merger->addRaw($data);
        }

        $createdPdf = $merger->merge();

        return response()->streamDownload(function () use ($createdPdf) {
            echo $createdPdf;
        }, 'browsershot-'.date('YmdHis').'.pdf');
    }
}
