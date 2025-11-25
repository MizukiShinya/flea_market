<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $profile = Auth::user()->profile;
        Comment::create([
            'profile_id' => $profile->id,
            'item_id' => $item->id,
            'content' => $request->validated()['content'],
        ]);

        return redirect()->back();
    }
}
