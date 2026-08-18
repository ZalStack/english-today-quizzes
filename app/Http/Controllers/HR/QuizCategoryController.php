<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\QuizCategory;
use Illuminate\Http\Request;

class QuizCategoryController extends Controller
{
    public function index()
    {
        $totalCategories = QuizCategory::count();
        $totalQuizzesInCategories = \App\Models\Quiz::count();
        $averagePerCategory = $totalCategories > 0 ? round($totalQuizzesInCategories / $totalCategories, 1) : 0;

        $categories = QuizCategory::withCount('quizzes')->paginate(10);
        return view('hr.categories.index', compact('categories', 'totalCategories', 'totalQuizzesInCategories', 'averagePerCategory'));
    }

    public function create()
    {
        return view('hr.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:quiz_categories',
            'description' => 'nullable|string|max:1000',
        ]);

        QuizCategory::create($validated);

        return redirect()->route('hr.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(QuizCategory $category)
    {
        return view('hr.categories.edit', compact('category'));
    }

    public function update(Request $request, QuizCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:quiz_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return redirect()->route('hr.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(QuizCategory $category)
    {
        if ($category->quizzes()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing quizzes.');
        }

        $category->delete();

        return redirect()->route('hr.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
