<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Show search results for stays, flights, flight+hotel, or car rental.
     * Returns mock results so the search "works" end-to-end.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'stays');
        $query = $request->get('q', '');
        $checkIn = $request->get('check_in');
        $checkOut = $request->get('check_out');
        $adults = (int) $request->get('adults', 2);
        $children = (int) $request->get('children', 0);
        $rooms = (int) $request->get('rooms', 1);
        $addFlights = $request->has('add_flights');
        $from = $request->get('from');
        $to = $request->get('to');
        $flightDate = $request->get('flight_date');
        $pickupDate = $request->get('pickup_date');
        $returnDate = $request->get('return_date');

        $types = [
            'stays' => 'Verblijf',
            'flights' => 'Vluchten',
            'flight_hotel' => 'Vlucht + Hotel',
            'car' => 'Auto huren',
        ];
        $typeLabel = $types[$type] ?? 'Verblijf';

        $results = $this->getMockResults($type, $query, $from, $to);

        return view('search.index', compact(
            'type', 'typeLabel', 'query', 'checkIn', 'checkOut',
            'adults', 'children', 'rooms', 'addFlights',
            'from', 'to', 'flightDate', 'pickupDate', 'returnDate',
            'results'
        ));
    }

    private function getMockResults(string $type, string $query, ?string $from, ?string $to): array
    {
        $destination = $query ?: $to ?: 'bestemming';
        $destSlug = strtolower(preg_replace('/\s+/', '_', $destination));
        if (strlen($destSlug) > 20) {
            $destSlug = substr($destSlug, 0, 20);
        }

        if ($type === 'stays' || $type === 'flight_hotel') {
            return [
                [
                    'name' => 'Hotel ' . ($query ?: 'Central') . ' Plaza',
                    'location' => $query ?: 'Amsterdam',
                    'rating' => 8.5,
                    'reviews' => 1243,
                    'price' => 89,
                    'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=300&fit=crop',
                    'type' => 'Hotel',
                ],
                [
                    'name' => 'Apartments ' . ($query ?: 'Downtown') . ' View',
                    'location' => $query ?: 'Amsterdam',
                    'rating' => 9.0,
                    'reviews' => 892,
                    'price' => 120,
                    'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop',
                    'type' => 'Appartement',
                ],
                [
                    'name' => 'B&B ' . ($query ?: 'Garden') . ' House',
                    'location' => $query ?: 'Amsterdam',
                    'rating' => 8.8,
                    'reviews' => 456,
                    'price' => 75,
                    'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop',
                    'type' => 'Bed & breakfast',
                ],
            ];
        }

        if ($type === 'flights') {
            $dep = $from ?: 'Amsterdam';
            $arr = $to ?: 'Barcelona';
            return [
                [
                    'airline' => 'KLM',
                    'from' => $dep,
                    'to' => $arr,
                    'departure' => '08:30',
                    'arrival' => '10:45',
                    'duration' => '2u 15m',
                    'price' => 129,
                    'stops' => 'Direct',
                ],
                [
                    'airline' => 'Transavia',
                    'from' => $dep,
                    'to' => $arr,
                    'departure' => '14:20',
                    'arrival' => '16:35',
                    'duration' => '2u 15m',
                    'price' => 79,
                    'stops' => 'Direct',
                ],
                [
                    'airline' => 'Ryanair',
                    'from' => $dep,
                    'to' => $arr,
                    'departure' => '18:00',
                    'arrival' => '20:20',
                    'duration' => '2u 20m',
                    'price' => 45,
                    'stops' => 'Direct',
                ],
            ];
        }

        if ($type === 'car') {
            return [
                [
                    'name' => 'Economy – VW Polo of gelijkwaardig',
                    'supplier' => 'Europcar',
                    'price' => 42,
                    'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400&h=300&fit=crop',
                ],
                [
                    'name' => 'Compact – Ford Focus of gelijkwaardig',
                    'supplier' => 'Hertz',
                    'price' => 55,
                    'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400&h=300&fit=crop',
                ],
                [
                    'name' => 'Midsize – BMW 3-serie of gelijkwaardig',
                    'supplier' => 'Sixt',
                    'price' => 89,
                    'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400&h=300&fit=crop',
                ],
            ];
        }

        return [];
    }
}
