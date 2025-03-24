<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Song;


class AlbumController extends Controller{
    public function getPopularAlbums(Request $request){
        $albums = Album::orderBy('id', 'desc')->limit(10)->get();
        return response()->json($albums);
    }

    public function index(){
        return response()->json(Album::all());
    }
    public function show($id){
        $album = Album::with('songs')->findOrFail($id);
        if(!$album){
            return response()->json(['message' => 'Không tìm thấy Album'], 404);
        }
        return response()->json($album);
    }
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required|string',
            'artist' => 'required|string'
        ]);
        $album = Album::create([
           'name' => $request->name,
              'artist' => $request->artist
        ]);
        return response()->json(['message' => 'Đã tạo thành công Album', 'Album' => $album], 201);
    }

    public function update(Request $request, $id){
        $album = Album::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string',
            'artist' => 'required|string'
        ]);
        $album->update([
            'name' => $request->name,
            'artist' => $request->artist,
        ]);
        return response()->json(['message' => 'Đã cập nhật Album thành công','album' => $album], 201);
    }

    public function destroy($id){
        $album = Album::findOrFail($id);
        $album->delete();
        return response()->json(['message' => 'Đã xóa Album thành công'], 201);
    }

    public function addSongToAlbum(Request $request, $albumId){
        $album = Album::findOrFail($albumId);
        $this->validate($request, ['id' => 'required|exists:songs,id']);
        $song = Song::findOrFail($request->id);
        if($album->songs()->where('id', $song->id)->exists()){
            return response()->json(['message'=> "Bài hát đã tồn tại trong Album"], 400);
        }
        $song->album_id = $album->id;
        $song->save();
        return response()->json(['message' => "Đã thêm bài hát vào Album thành công"], 201);
    }

    public function removeSongToAlbum(Request $request, $albumId){
        $album = Album::findOrFail($albumId);
        $this->validate($request, ['id' => 'required|exists:songs,id']);
        $song = Song::findOrFail($request->id);
        if(!$album->songs()->where('id', $song->id)->exists()){
            return response()->json(['message'=> "Bài hát không tồn tại trong Album"], 400);
        }
        $song->album_id = null;
        $song->save();
        return response()->json(['message' => "Đã xóa bài hát khỏi Album thành công"], 201);
    }

}