<?php
namespace App\Http\Controllers;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller{
    public function getTrendingSongs(){
        $songs = Song::orderBy('id', 'desc')->limit(10)->get();
        return response()->json($songs);
    }

    public function index(){
        return response()->json(Song::orderBy('id', 'desc')->limit(10)->get());
    }

    public function show($id){
        $song = Song::findOrFail($id);
        return response()->json($song);
    }

    public function store(Request $request){
        // $this->validate($request, [
        //     'songs' => 'required|array',
        //     'songs.*.name' => 'required|string',
        //     'songs.*.artist' => 'required|string',
        //     'songs.*.duration' => 'required|integer',
        // ]);
    
        // $songs = [];
    
        // foreach ($request->songs as $songData) {
        //     $songs[] = Song::create([
        //         'name' => $songData['name'],
        //         'artist' => $songData['artist'],
        //         'duration' => $songData['duration'],
        //     ]);
        // }
    
        // return response()->json([
        //     'message' => 'Đã tạo thành công các bài hát',
        //     'songs' => $songs
        // ], 201);

        $this->validate($request,[
            'name' => 'required|string',
            'artist' => 'required|string',
            'duration' => 'required|integer',
        ]);
        if($request->name){
            $song = Song::where('name', $request->name)->first();
            if($song){
                return response()->json(['message' => 'Bài hát đã tồn tại'], 400);
            }
        }
        $song = Song::create([
            'name' => $request->name,
            'artist' => $request->artist,
            'duration' => $request->duration,
        ]);
        return response()->json(['message' => 'Đã tạo thành công Bài hát', 'song', $song], 201);
    }
    public function update(Request $request, $id){
        $song = Song::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string',
            'artist' => 'required|string',
            'duration' => 'required|integer',
        ]);
        $song->update([
            'name' => $request->name,
            'artist' => $request->artist,
            'duration' => $request->duration,
        ]);
        return response()->json(['message' => 'Đã cập nhật Bài hát thành công', 'song' => $song], 201);
    }
    public function destroy($id){
        $song = Song::findOrFail($id);
        $song->delete();
        return response()->json(['message' => 'Đã xóa Bài hát thành công'], 201);
    }
}