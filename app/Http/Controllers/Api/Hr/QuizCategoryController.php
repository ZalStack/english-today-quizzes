<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\QuizCategory;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class QuizCategoryController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(QuizCategory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category = QuizCategory::create($validated);
        return $this->success($category, 'Category created', 201);
    }

    public function show($id)
    {
        $category = QuizCategory::find($id);
        if (!$category) {
            return $this->error('Category not found', 404);
        }
        return $this->success($category);
    }

    public function update(Request $request, $id)
    {
        $category = QuizCategory::find($id);
        if (!$category) {
            return $this->error('Category not found', 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category->update($validated);
        return $this->success($category, 'Category updated');
    }

    public function destroy($id)
    {
        $category = QuizCategory::find($id);
        if (!$category) {
            return $this->error('Category not found', 404);
        }
        $category->delete();
        return $this->success(null, 'Category deleted');
    }
}
