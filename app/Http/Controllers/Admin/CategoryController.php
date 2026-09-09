<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('translations')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|max:255|alpha_dash|unique:categories,slug',
            'name_ne' => 'required_without:name_en|nullable|string|max:255',
            'name_en' => 'required_without:name_ne|nullable|string|max:255',
        ]);

        $category = Category::create(['slug' => Str::slug($data['slug'])]);

        if (!empty($data['name_ne'])) {
            CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'ne', 'name' => $data['name_ne']]);
        }
        if (!empty($data['name_en'])) {
            CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'en', 'name' => $data['name_en']]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $category->load('translations');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name_ne' => 'required_without:name_en|nullable|string|max:255',
            'name_en' => 'required_without:name_ne|nullable|string|max:255',
        ]);

        if (!empty($data['name_ne'])) {
            CategoryTranslation::updateOrCreate(['category_id' => $category->id, 'locale' => 'ne'], ['name' => $data['name_ne']]);
        }
        if (!empty($data['name_en'])) {
            CategoryTranslation::updateOrCreate(['category_id' => $category->id, 'locale' => 'en'], ['name' => $data['name_en']]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}