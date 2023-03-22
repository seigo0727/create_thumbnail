<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $html = '';
        $list = [1,2,3,4];
        foreach($list as $value) {
            $html .= view('test', [
                'value' => $value
            ])->render();
        }
        return view('edit', [
            'html' => $html,
        ]);
    }
}
