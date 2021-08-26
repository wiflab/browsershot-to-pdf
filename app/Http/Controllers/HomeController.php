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
        $request->validate([
            'type' => 'required|in:html,url',
            'data' => 'required|array',
        ]);

        $type = $request->input('type');
        $data = $request->input('data');

        $merger = new Merger;

        foreach ($data as $content) {
            $rawPdf = ($type === 'html') ? Browsershot::html($content) : Browsershot::url($content);

            if (config('app.node_path')) {
                $rawPdf->setNodeBinary(config('app.node_path'));
            }
            if (config('app.npm_path')) {
                $rawPdf->setNpmBinary(config('app.npm_path'));
            }

            $merger->addRaw($rawPdf->addChromiumArguments([
                                'no-sandbox',
                                'disable-setuid-sandbox'
                            ])->pdf());
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
