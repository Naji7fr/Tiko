<?php

namespace App\Http\Controllers;

use App\Models\Medewerker;
use Illuminate\Http\Request;

// This is the MedewerkerController class, which handles CRUD operations for Medewerkers (Employees) in the Laravel application.
// It extends the base Controller class provided by Laravel.
class MedewerkerController extends Controller
{
    // The index method displays an overview of all medewerkers.
    // It retrieves all Medewerker records from the database and passes them to the view.
    public function index()
    {
        // Fetch all medewerkers from the database using the Medewerker model.
        $medewerkers = Medewerker::all();
        // Return the view 'medewerkers.index' with the medewerkers data compacted.
        return view('medewerkers.index', compact('medewerkers'));
    }

    // The create method shows the form to add a new medewerker.
    // This method does not interact with the database; it just renders the form view.
    public function create()
    {
        // Return the view 'medewerkers.create' to display the form for creating a new medewerker.
        return view('medewerkers.create');
    }

    // The store method handles the creation of a new medewerker.
    // It validates the incoming request data and creates a new Medewerker record in the database.
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data according to the specified rules.
            $validated = $request->validate([
                'naam' => 'required|string|max:255',
                'email' => 'required|email|unique:medewerkers,email',
                'status' => 'required|in:Actief,Inactief,Op proef,Afwezig,Gepauzeerd',
                'type' => 'required|in:Admin,Financieel Medewerker,Reisadviseur,Boekingsagent',
            ]);

            // Create a new Medewerker record in the database using the validated data.
            Medewerker::create($validated);

            // Redirect to the index page with a success message.
            return redirect()->route('medewerkers.index')->with('success', 'Medewerker toegevoegd!');
        } catch (\Exception $e) {
            // If an exception occurs (e.g., validation failure, database error), catch it and redirect back with an error message.
            return back()->withErrors(['An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    // The show method displays details of a single medewerker.
    // It finds the medewerker by ID and passes it to the view.
    public function show($id)
    {
        try {
            // Find the Medewerker by ID. If not found, throw a ModelNotFoundException.
            $medewerker = Medewerker::findOrFail($id);

            // Return the view 'medewerkers.show' with the medewerker data.
            return view('medewerkers.show', compact('medewerker'));
        } catch (\Exception $e) {
            // If an exception occurs (e.g., medewerker not found), catch it and redirect back with an error message.
            return back()->withErrors(['An error occurred: ' . $e->getMessage()]);
        }
    }

    // The edit method shows the form to edit an existing medewerker.
    // It finds the medewerker by ID and passes it to the edit view.
    public function edit($id)
    {
        try {
            // Find the Medewerker by ID. If not found, throw a ModelNotFoundException.
            $medewerker = Medewerker::findOrFail($id);

            // Return the view 'medewerkers.edit' with the medewerker data for editing.
            return view('medewerkers.edit', compact('medewerker'));
        } catch (\Exception $e) {
            // If an exception occurs (e.g., medewerker not found), catch it and redirect back with an error message.
            return back()->withErrors(['An error occurred: ' . $e->getMessage()]);
        }
    }

    // The update method handles updating an existing medewerker.
    // It validates the request data, checks for uniqueness, and updates the record.
    public function update(Request $request, $id)
    {
        try {
            // Find the Medewerker by ID. If not found, throw a ModelNotFoundException.
            $medewerker = Medewerker::findOrFail($id);

            // Validate the incoming request data.
            // Similar rules as store, but email uniqueness is checked differently for updates.
            $validated = $request->validate([
                'naam' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'status' => 'required|in:Actief,Inactief,Op proef,Afwezig,Gepauzeerd',
                'type' => 'required|in:Admin,Financieel Medewerker,Reisadviseur,Boekingsagent',
            ]);

            // Check if another medewerker already has the same naam and email combination.
            // This ensures uniqueness across records except for the current one being updated.
            $exists = Medewerker::where('naam', $validated['naam'])
                ->where('email', $validated['email'])
                ->where('id', '!=', $medewerker->id)
                ->exists();

            // If such a combination exists, return back with an error message.
            if ($exists) {
                return back()->withErrors(['De combinatie van naam en e-mailadres bestaat al bij een andere medewerker.'])->withInput();
            }

            // Update the medewerker record with the validated data.
            $medewerker->update($validated);

            // Redirect to the index page with a success message.
            return redirect()->route('medewerkers.index')->with('success', 'Medewerker gewijzigd!');
        } catch (\Exception $e) {
            // If an exception occurs (e.g., validation failure, database error), catch it and redirect back with an error message.
            return back()->withErrors(['An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    // The destroy method handles deleting a medewerker.
    // It finds the medewerker by ID and deletes it from the database.
    public function destroy($id)
    {
        try {
            // Find the Medewerker by ID. If not found, throw a ModelNotFoundException.
            $medewerker = Medewerker::findOrFail($id);

            // Delete the medewerker record from the database.
            $medewerker->delete();

            // Redirect to the index page with a success message.
            return redirect()->route('medewerkers.index')->with('success', 'Medewerker verwijderd!');
        } catch (\Exception $e) {
            // If an exception occurs (e.g., medewerker not found, database error), catch it and redirect back with an error message.
            return back()->withErrors(['An error occurred: ' . $e->getMessage()]);
        }
    }
}
