<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminSearchService;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function __construct(private AdminSearchService $searchService) {}

    public function index(Request $request)
    {
        $query   = trim($request->get('q', ''));
        $results = $query ? $this->searchService->search($query) : [];

        return view('admin.search.results', compact('query', 'results'));
    }
}
