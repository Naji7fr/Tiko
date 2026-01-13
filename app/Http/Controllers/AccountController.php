<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = User::all();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:patient,tandarts,praktijkmanager,assistent',
            'status' => 'required|in:Actief,Inactief',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        return redirect()->route('accounts.index')->with('success', 'Account toegevoegd!');
    }

    public function show($id)
    {
        $account = User::findOrFail($id);
        return view('accounts.show', compact('account'));
    }

    public function edit($id)
    {
        $account = User::findOrFail($id);
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:patient,tandarts,praktijkmanager,assistent',
            'status' => 'required|in:Actief,Inactief',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $account->update($validated);
        return redirect()->route('accounts.index')->with('success', 'Account bijgewerkt!');
    }

    public function destroy($id)
    {
        $account = User::findOrFail($id);
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account verwijderd!');
    }
}
