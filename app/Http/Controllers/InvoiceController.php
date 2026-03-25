<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles all CRUD operations for invoices (facturen).
 *
 * Routes are protected by the admin/manager middleware
 * defined in routes/web.php.
 */
class InvoiceController extends Controller
{
    /**
     * Display a list of all invoices including their related klant.
     * Falls back to an empty list with an error message on failure.
     */
    public function index(): View
    {
        try {
            // Eager-load the patient relation to avoid N+1 queries in the view.
            $invoices = Invoice::with('patient')->get();

            return view('facturen.index', compact('invoices'));
        } catch (\Exception $e) {
            return view('facturen.index', ['invoices' => collect()])
                ->with('error', 'Factuuroverzicht kon niet worden geladen: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new invoice.
     * Only users with the 'klant' role are shown as selectable recipients.
     */
    public function create(): View
    {
        try {
            $patients = User::where('role', 'klant')->get();

            return view('facturen.create', compact('patients'));
        } catch (\Exception $e) {
            // Return the form with an empty dropdown so the page still renders.
            return view('facturen.create', ['patients' => collect()])
                ->with('error', 'Klanten konden niet worden geladen: ' . $e->getMessage());
        }
    }

    /**
     * Validate and save a new invoice to the database.
     *
     * The factuurnummer must be unique across all invoices.
     * ValidationException is re-thrown so Laravel can redirect back
     * and display inline field errors automatically.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate(
                [
                    'factuurnummer' => 'required|string|max:50|unique:invoices,factuurnummer',
                    'patient_id'    => 'required|exists:users,id',
                    'amount'        => 'required|numeric|min:0.01',
                    'due_date'      => 'required|date|after:today',  // must be a future date
                    'status'        => 'required|in:openstaand,betaald,vervallen',
                    'description'   => 'nullable|string|max:1000',
                ],
                [
                    // Custom Dutch error message for duplicate invoice numbers.
                    'factuurnummer.unique' => 'Factuur nummer bestaat al.',
                ]
            );

            Invoice::create($validated);

            return redirect()
                ->route('facturen.index')
                ->with('success', 'Factuur succesvol aangemaakt!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw so Laravel's default error handling redirects back with field errors.
            throw $e;
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Factuur kon niet worden aangemaakt: ' . $e->getMessage());
        }
    }

    /**
     * Show the edit form for an existing invoice.
     * Redirects back to the index if the invoice cannot be found.
     */
    public function edit(int|string $id): View|RedirectResponse
    {
        try {
            $invoice  = Invoice::findOrFail($id);
            $patients = User::where('role', 'klant')->get();

            return view('facturen.edit', compact('invoice', 'patients'));
        } catch (\Exception $e) {
            return redirect()
                ->route('facturen.index')
                ->with('error', 'Factuur niet gevonden: ' . $e->getMessage());
        }
    }

    /**
     * Validate and update an existing invoice.
     *
     * The unique rule on factuurnummer ignores the current record's own ID
     * so saving without changing the number does not trigger a duplicate error.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);

            $validated = $request->validate(
                [
                    // Exclude the current invoice from the uniqueness check.
                    'factuurnummer' => 'required|string|max:50|unique:invoices,factuurnummer,' . $id,
                    'patient_id'    => 'required|exists:users,id',
                    'amount'        => 'required|numeric|min:0.01',
                    'due_date'      => 'required|date',
                    'status'        => 'required|in:openstaand,betaald,vervallen',
                    'description'   => 'nullable|string|max:1000',
                ],
                [
                    'factuurnummer.unique' => 'Factuur nummer bestaat al.',
                ]
            );

            $invoice->update($validated);

            return redirect()
                ->route('facturen.index')
                ->with('success', 'Factuur succesvol bijgewerkt!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw so Laravel's default error handling redirects back with field errors.
            throw $e;
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Factuur kon niet worden bijgewerkt: ' . $e->getMessage());
        }
    }

    /**
     * Delete an invoice from the database.
     * Redirects to the index with an error message if deletion fails.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->delete();

            return redirect()
                ->route('facturen.index')
                ->with('success', 'Factuur succesvol verwijderd!');
        } catch (\Exception $e) {
            return redirect()
                ->route('facturen.index')
                ->with('error', 'Factuur kon niet worden verwijderd: ' . $e->getMessage());
        }
    }
}
