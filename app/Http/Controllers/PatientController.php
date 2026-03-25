<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $appointments = Appointment::where('patient_id', $user->id)->with('dentist')->orderBy('appointment_date', 'desc')->get();
        $invoices = Invoice::where('patient_id', $user->id)->orderBy('due_date', 'desc')->get();
        $messages = Message::where('receiver_id', $user->id)
            ->orWhere(function($query) {
                $query->whereNull('receiver_id');
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patient.dashboard', compact('appointments', 'invoices', 'messages'));
    }

    public function appointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('patient_id', $user->id)->with('dentist')->orderBy('appointment_date', 'desc')->get();
        $dentists = User::where('role', 'reisadviseur')->get();
        
        return view('patient.appointments', compact('appointments', 'dentists'));
    }

    public function storeAppointment(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'dentist_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['patient_id'] = $user->id;
        $validated['status'] = 'gepland';

        Appointment::create($validated);
        return redirect()->route('patient.appointments')->with('success', 'Afspraak succesvol aangemaakt!');
    }

    public function invoices()
    {
        $user = Auth::user();
        $invoices = Invoice::where('patient_id', $user->id)->orderBy('due_date', 'desc')->get();
        
        return view('patient.invoices', compact('invoices'));
    }

    public function messages()
    {
        $user = Auth::user();
        $messages = Message::where('receiver_id', $user->id)
            ->orWhere(function($query) {
                $query->whereNull('receiver_id');
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();
        $users = User::where('id', '!=', $user->id)->get();
        
        return view('patient.messages', compact('messages', 'users'));
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        $validated['sender_id'] = $user->id;
        $validated['is_read'] = false;

        Message::create($validated);
        return redirect()->route('patient.messages')->with('success', 'Bericht succesvol verzonden!');
    }
}

