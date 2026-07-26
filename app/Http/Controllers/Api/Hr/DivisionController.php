<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class DivisionController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(Division::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $division = Division::create($validated);
        return $this->success($division, 'Division created', 201);
    }

    public function show($id)
    {
        $division = Division::find($id);
        if (!$division) {
            return $this->error('Division not found', 404);
        }
        return $this->success($division);
    }

    public function update(Request $request, $id)
    {
        $division = Division::find($id);
        if (!$division) {
            return $this->error('Division not found', 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);
        $division->update($validated);
        return $this->success($division, 'Division updated');
    }

    public function destroy($id)
    {
        $division = Division::find($id);
        if (!$division) {
            return $this->error('Division not found', 404);
        }
        $division->delete();
        return $this->success(null, 'Division deleted');
    }
}
