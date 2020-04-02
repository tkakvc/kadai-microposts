<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function store(Request $request, $id)
    {
        \Auth::user()->favorite($id);
        return back();
    }

    public function destroy($id)
    {
        \Auth::user()->unfavorite($id);
        return back();
    }
    public function favorites($id)
    {
        $user = \Auth::user();
        $favorite = $user->favorites()->paginate(10);

        $data = [
            'user' => $user,
            'microposts' => $favorite,
        ];

        $data += $this->counts($user);

        return view('users.favorites', $data);
    }
}
