<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Models\PlaylistSong;
use App\Models\Song;

class PlaylistController extends Controller{
    public function index(Request $request){
        $user = $request->user();
        return response()->json(Playlist::where('user_id', $user->id)->get());
    }

    public function store(Request $request){
        $user = $request->user();
        $this->validate($request, [
            'name' => 'required|string',
        ]);
        $playlist = Playlist::create(['name' => $request->name, 'user_id' => $user->id]);
        return response()->json(['message' => 'Đã tạo thành công Playlist','playlist'=> $playlist], 201);
    }

    public function show(Request $request, $id){
        $playlist = Playlist::with('songs')->findOrFail($id);
        if(!$playlist){
            return response()->json(['message' => 'Không tìm thấy Playlist'], 404);
        }
        if($playlist->user_id != $request->user()->id){
            return response()->json(['message' => 'Bạn không có quyền xem playlist này'], 403);
        }
        return response()->json($playlist);
    }

    public function update(Request $request, $id){

        $playlist = Playlist::findOrFail($id);
        if($playlist->user_id != $request->user()->id){
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa playlist này'], 403);
        }
        $this->validate($request,['name' => 'required|string']);
        $playlist-> update(['name' => $request->name]);
        return response()->json(['message' => 'Đã cập nhật Playlist thành công','playlist' => $playlist],201);
    }

    public function destroy(Request $request, $id){
        $playlist = Playlist::findOrFail($id);
        if($playlist->user_id != $request->user()->id){
            return response()->json(['message' => 'Bạn không có quyền xóa playlist này'], 403);
        }
        $playlist->delete();
        return response()->json(['message' => 'Đã xóa Playlist thành công'], 201);
    }

    // Add Song to Playlist
    public function addSongToPlaylist(Request $request, $playlistId)
    {
        try {
            // Lấy Playlist và kiểm tra quyền sở hữu
            $playlist = Playlist::findOrFail($playlistId);
            
            if ($playlist->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Bạn không có quyền thêm bài hát vào playlist này'
                ], 403);
            }
            $this->validate($request,['id' => 'required|exists:songs,id']);
            

            $songId = $request->input('id');

            // Kiểm tra nếu bài hát đã tồn tại trong Playlist
            $exists = $playlist->songs()
                ->where('song_id', $songId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Bài hát đã tồn tại trong playlist'
                ], 400);
            }

            // Tạo mới bản ghi sử dụng relationship
            $playlistSong = $playlist->songs()->attach($songId);

            // Lấy thông tin playlist mới cập nhật
            $playlist->load('songs');

            return response()->json([
                'message' => 'Đã thêm bài hát vào playlist thành công',
                'playlist' => $playlist
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm bài hát vào playlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeSongToPlaylist(Request $request, $playlistId)
    {
        try {
            // Lấy Playlist và kiểm tra quyền sở hữu
            $playlist = Playlist::findOrFail($playlistId);
            
            if ($playlist->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Bạn không có quyền thêm bài hát vào playlist này'
                ], 403);
            }
            $this->validate($request,['id' => 'required|exists:songs,id']);
            

            $songId = $request->input('id');

            // Kiểm tra nếu bài hát đã tồn tại trong Playlist
            $exists = $playlist->songs()
                ->where('song_id', $songId)
                ->exists();

            if (!$exists) {
                return response()->json([
                    'message' => 'Bài không tồn tại trong playlist'
                ], 400);
            }

            // Tạo mới bản ghi sử dụng relationship
            $playlistSong = $playlist->songs()->detach($songId);

            // Lấy thông tin playlist mới cập nhật
            $playlist->load('songs');

            return response()->json([
                'message' => 'Đã xóa bài hát vào playlist thành công',
                'playlist' => $playlist
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm bài hát vào playlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}