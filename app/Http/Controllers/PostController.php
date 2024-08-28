<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Services\PHPMailerService;


class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with('user')->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|min:3',
            'content' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpg,png,mp4,mp3|max:10240',
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('media', 'public');
            $validatedData['media_path'] = $path;
        }

        $validatedData['user_id'] = auth()->id();
        $post = Post::create($validatedData);

        // Send notification email
        // $this->sendPostCreatedNotification(auth()->user()->email);

        $this->mailer->send(auth()->user()->email, auth()->user()->name, $subject, $body);


        return redirect()->route('posts.index');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validatedData = $request->validate([
            'title' => 'required|min:3',
            'content' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpg,png,mp4,mp3|max:10240',
        ]);

        if ($request->hasFile('media')) {
            if ($post->media_path) {
                Storage::disk('public')->delete($post->media_path);
            }
            $path = $request->file('media')->store('media', 'public');
            $validatedData['media_path'] = $path;
        }

        $post->update($validatedData);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        return redirect()->route('posts.index');
    }

}
