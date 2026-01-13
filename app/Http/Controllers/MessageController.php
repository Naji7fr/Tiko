<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with('sender', 'receiver')->get();
        return view('berichten.index', compact('messages'));
    }

    public function create()
    {
        $currentUserId = Auth::id() ?? 0;
        $users = User::where('id', '!=', $currentUserId)->get();
        return view('berichten.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        // Use first admin user if no authenticated user, or allow manual selection
        $validated['sender_id'] = Auth::id() ?? User::where('role', 'admin')->first()->id ?? 1;
        $validated['is_read'] = false;

        Message::create($validated);
        return redirect()->route('berichten.index')->with('success', 'Bericht succesvol verzonden!');
    }

    public function show($id)
    {
        $message = Message::with('sender', 'receiver')->findOrFail($id);
        $message->update(['is_read' => true]);
        return view('berichten.show', compact('message'));
    }

    public function edit($id)
    {
        $message = Message::findOrFail($id);
        $currentUserId = Auth::id() ?? 0;
        $users = User::where('id', '!=', $currentUserId)->get();
        return view('berichten.edit', compact('message', 'users'));
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        $message->update($validated);
        return redirect()->route('berichten.index')->with('success', 'Bericht succesvol bijgewerkt!');
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        return redirect()->route('berichten.index')->with('success', 'Bericht succesvol verwijderd!');
    }
}
