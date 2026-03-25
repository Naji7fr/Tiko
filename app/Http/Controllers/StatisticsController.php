<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        $totalPatients = User::where('role', 'klant')->count();
        $totalDentists = User::where('role', 'reisadviseur')->count();
        $totalAppointments = Appointment::count();
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'betaald')->count();
        $overdueInvoices = Invoice::where('status', 'vervallen')->count();

        return view('statistieken.index', compact(
            'totalPatients',
            'totalDentists',
            'totalAppointments',
            'totalInvoices',
            'paidInvoices',
            'overdueInvoices'
        ))->with('success', 'Dashboard succesvol geladen');
    }
}
