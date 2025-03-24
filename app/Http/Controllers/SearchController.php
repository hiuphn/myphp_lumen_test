<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\Album;

class SearchController extends Controller{
    public function search(Request $request){
        $query = $request->input('query');
        $playlists = Playlist::where('name', 'like', "%$query%");
        $songs = Song::where('name', 'like', "%$query%");
        $albums = Album::where('name', 'like', "%$query%");
        return response()->json([
            'playlists' => $playlists->get(),
            'songs' => $songs->get(),
            'albums' => $albums->get()
        ]);
    }
}