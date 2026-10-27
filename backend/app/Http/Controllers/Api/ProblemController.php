<?php

namespace App\Http\Controllers\Api;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $problems = \App\Models\Problem::with('topics')->paginate(15);
        return response()->json($problems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
