<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->paginate(10);
        return view('hr.divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('hr.divisions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions',
            'description' => 'nullable|string|max:1000',
        ]);

        Division::create($validated);

        return redirect()->route('hr.divisions.index')
            ->with('success', 'Division created successfully.');
    }

    public function edit(Division $division)
    {
        return view('hr.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $division->update($validated);

        return redirect()->route('hr.divisions.index')
            ->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        if ($division->users()->count() > 0) {
            return back()->with('error', 'Cannot delete division with existing employees.');
        }

        $division->delete();

        return redirect()->route('hr.divisions.index')
            ->with('success', 'Division deleted successfully.');
    }
}
