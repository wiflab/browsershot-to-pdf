<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'required|in:html,url',
            'data' => 'required|array',
            'format' => 'nullable|string',
        ]);

        $type = $request->input('type');
        $data = $request->input('data');
        $format = $request->input('format');

        $files = [];
        foreach ($data as $content) {
            $rawPdf = ($type === 'html') ? Browsershot::html($content) : Browsershot::url($content);

            if (! is_null($format)) {
                $rawPdf = $rawPdf->format($format);
            }

            if (config('app.node_path')) {
                $rawPdf->setNodeBinary(config('app.node_path'));
            }
            if (config('app.npm_path')) {
                $rawPdf->setNpmBinary(config('app.npm_path'));
            }

            $temporaryFile = tempnam('/tmp', config('app.name'));
            $rawPdf->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox',
            ])->savePdf($temporaryFile);
            $files[] = $temporaryFile;
        }

        $temporaryFinalFile = tempnam('/tmp', config('app.name'));
        exec('pdftk '.implode(' ', $files).' cat output '.$temporaryFinalFile);

        return response()->download($temporaryFinalFile, 'browsershot-'.date('YmdHis').'.pdf');
    }

    public function redirect()
    {
        return redirect(config('app.home_redirect'));
    }

    public function saveImage(Request $request)
    {
        $request->validate([
            'type' => 'required|in:html,url',
            'data' => 'required',
        ]);

        $url = $request->input('data');
        $image = [];

        $rawPdf = Browsershot::url($url);

        if (config('app.node_path')) {
            $rawPdf->setNodeBinary(config('app.node_path'));
        }
        if (config('app.npm_path')) {
            $rawPdf->setNpmBinary(config('app.npm_path'));
        }

        $image = $rawPdf->addChromiumArguments([
            'no-sandbox',
            'disable-setuid-sandbox',
        ])->windowSize(1920, 1080)
          ->screenshot();

        return $image;
    }
}
