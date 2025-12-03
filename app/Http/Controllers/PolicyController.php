<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class PolicyController extends Controller
{
    public function index()
    {
        $policy = Policy::first();

        return view('policy.index', compact('policy'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        if (Policy::count() > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only one policy is allowed. Please edit the existing policy.'
                ], 422);
            }
            return redirect()->route('policy.index')
                ->with('error', 'Only one policy is allowed. Please edit the existing policy.');
        }

        $policy = Policy::create([
            'title' => $request->title,
            'content' => $request->input('content'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Policy created successfully!',
                'policy' => $policy
            ]);
        }

        return redirect()->route('policy.index')->with('success', 'Policy created successfully!');
    }

    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $policy->update([
            'title' => $request->title,
            'content' => $request->input('content'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Policy updated successfully!',
                'policy' => $policy
            ]);
        }

        return redirect()->route('policy.index')->with('success', 'Policy updated successfully!');
    }

    //     public function uploadPdf(Request $request)
    //     {
    //         $request->validate([
    //             'pdf_file' => 'required|mimes:pdf|max:10000',
    //         ]);

    //         $file = $request->file('pdf_file');

    //         // Use Smalot parser to extract plain text (layout won't be preserved)
    //         $parser = new Parser();
    //         $pdf = $parser->parseFile($file->getPathname());
    //         $text = $pdf->getText();

    //         // Convert newlines to <br> for TinyMCE
    //         $htmlContent = nl2br($text);

    //         // Get existing policy or create a new one
    //         $policy = Policy::first();
    //         if (!$policy) {
    //             $policy = new Policy();
    //         }

    //         // Set content temporarily, but don't save yet
    //         $policy->content = $htmlContent;

    //         return view('policy.index', compact('policy'));
    //     }

    public function uploadPdf(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:10000',
        ]);

        $file = $request->file('pdf_file');

        // Save the uploaded PDF temporarily
        $pdfPath = $file->getPathname();

        // Convert PDF → HTML using pdf2htmlEX
        $outputHtmlFile = storage_path('app/public/pdf_temp.html');
        exec("pdf2htmlEX --zoom 1.3 " . escapeshellarg($pdfPath) . " " . escapeshellarg($outputHtmlFile));

        // Read generated HTML
        $htmlContent = file_get_contents($outputHtmlFile);

        // Wrap in object so Blade doesn’t break
        $policy = (object)[
            'id' => null,
            'title' => '',
            'content' => $htmlContent
        ];

        return view('policy.index', compact('policy'));
    }
}
