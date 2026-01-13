<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        try {
            $invoices = Invoice::with('patient')->get();
            return view('facturen.index', compact('invoices'));
        } catch (\Exception $e) {
            return view('facturen.index', ['invoices' => collect()])->with('error', 'Factuuroverzicht kon niet worden geladen');
        }
    }

    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('facturen.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after:today',
            'status' => 'required|in:openstaand,betaald,vervallen',
            'description' => 'nullable|string|max:1000',
        ]);

        Invoice::create($validated);
        return redirect()->route('facturen.index')->with('success', 'Factuur succesvol aangemaakt!');
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $patients = User::where('role', 'patient')->get();
        return view('facturen.edit', compact('invoice', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'status' => 'required|in:openstaand,betaald,vervallen',
            'description' => 'nullable|string|max:1000',
        ]);

        $invoice->update($validated);
        return redirect()->route('facturen.index')->with('success', 'Factuur succesvol bijgewerkt!');
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('facturen.index')->with('success', 'Factuur succesvol verwijderd!');
    }
}
