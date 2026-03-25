<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('patient', 'dentist')->get();
        return view('afspraken.index', compact('appointments'));
    }

    public function create()
    {
        $patients = User::where('role', 'klant')->get();
        $dentists = User::where('role', 'reisadviseur')->get();
        return view('afspraken.create', compact('patients', 'dentists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'dentist_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:gepland,bevestigd,voltooid,geannuleerd',
        ]);

        Appointment::create($validated);
        return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol aangemaakt!');
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $patients = User::where('role', 'klant')->get();
        $dentists = User::where('role', 'reisadviseur')->get();
        return view('afspraken.edit', compact('appointment', 'patients', 'dentists'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'dentist_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:gepland,bevestigd,voltooid,geannuleerd',
        ]);

        $appointment->update($validated);
        return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol bijgewerkt!');
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return redirect()->route('afspraken.index')->with('success', 'Afspraak succesvol verwijderd!');
    }
}
