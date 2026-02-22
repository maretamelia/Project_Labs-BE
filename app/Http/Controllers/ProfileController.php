<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function store(ProfileUpdateRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        if(!$user){
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        if(isset($data['password'])){
            $user->password = bcrypt($data['password']);
        }

        if($request->hasFile('image')){
            // Hapus gambar lama jika ada
            if($user->image){
                Storage::disk('public')->delete($user->image);
            }

            // Tentukan path penyimpanan
            $user->image = $request->file('image')->store('users', 'public');;

        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return response()->json([
            'message' => 'Berhasil memperbarui profil',
            'data' => $user,
        ]);
    }

    public function notification(Request $request)
    {
        $user = Auth::user();

        if(!$user){
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $notifications = Notification::where('to_user_id', $user->id)
            ->with(['toUser'])
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil notifikasi',
            'data' => $notifications,
        ]);
    }

    public function readNotification(Request $request, Notification $notification)
    {
        $user = Auth::user();

        if(!$user){
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        if($notification->to_user_id !== $user->id){
            return response()->json([
                'message' => 'Notifikasi tidak ditemukan',
            ], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'message' => 'Berhasil membaca notifikasi',
            'data' => $notification,
        ]);
    }
}
