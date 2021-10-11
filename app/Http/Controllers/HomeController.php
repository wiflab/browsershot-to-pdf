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
        ]);

        $type = $request->input('type');
        $data = $request->input('data');

        $files = [];
        foreach ($data as $content) {
            $rawPdf = ($type === 'html') ? Browsershot::html($content) : Browsershot::url($content);

            if (config('app.node_path')) {
                $rawPdf->setNodeBinary(config('app.node_path'));
            }
            if (config('app.npm_path')) {
                $rawPdf->setNpmBinary(config('app.npm_path'));
            }

            $temporaryFile = tempnam("/tmp", config('app.name'));
            $rawPdf->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox'
            ])->savePdf($temporaryFile);
            $files[] = $temporaryFile;
        }

        $temporaryFinalFile = tempnam("/tmp", config('app.name'));
        exec('pdftk '.implode(' ', $files).' cat output '.$temporaryFinalFile);

        return response()->download($temporaryFinalFile, 'browsershot-'.date('YmdHis').'.pdf');
    }

    public function redirect()
    {
        return redirect(config('app.home_redirect'));
    }
}
