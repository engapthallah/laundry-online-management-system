<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class PublicServiceController extends Controller
{
    /**
     * Display a listing of active services.
     */
    public function index()
    {
        $services = Service::where('is_active', true)
                           ->orderBy('name')
                           ->get();

        return view('public.services', compact('services'));
    }
}
