<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Colourisation;
use Illuminate\Http\Request;

class ColourisationController extends Controller
{
    /**
     * Show the form to create a new colourisation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('colourisation.create');
    }

    /**
     * Store the colourisation.
     *
     * @param \Illuminate\Http\Request $input
     *
     * @return \Illuminate\Http\Response
     *
     * TODO: Validate $input
     */
    public function store(\Illuminate\Http\Request $input)
    {
        $input->merge([
            'user_id'  => \Auth::user()->id,
            'original' => $this->_upload($input),
        ]);

        \App\Models\Colourisation::create($input->only([
            'user_id', 'original', 'title',
        ]));

        return redirect(url('/dashboard'));
    }

    /**
     * Upload a file.
     *
     * @param \Illuminate\Http\Request $input
     *
     * @return string Full path to the uploaded file.
     */
    private function _upload(\Illuminate\Http\Request $input)
    {
        // Make the user's folder if necessary
        if (!file_exists(config('colourise.original-path') . '/' . \Auth::user()->id)) {
            mkdir(config('colourise.original-path') . '/' . \Auth::user()->id, 0755, true);
        }

        // Get a unique filename
        do {
            $filename = str_random(20) . '.' . $input->file('file')->guessExtension();

        } while (file_exists(config('colourise.original-path') . '/' . \Auth::user()->id . '/' . $filename));

        // Put the file where it needs to go
        $input->file('file')->move(
            config('colourise.original-path') . '/' . \Auth::user()->id,
            $filename
        );

        // Return the path of the uploaded file
        return config('colourise.original-path') . '/' . \Auth::user()->id . '/' . $filename;
    }
}