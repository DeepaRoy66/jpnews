<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public array $layoutOptions = [
        'list'      => 'List (numbered rows)',
        'stat'      => 'Stat cards (2-column grid)',
        'big-grid'  => 'Big grid (hero + tiles)',
        'masonry'   => 'Masonry (Pinterest style)',
        'minimal'   => 'Minimal (horizontal rows)',
        'timeline'  => 'Timeline (vertical dots)',
        'icon-card' => 'Icon cards (2-column)',
    ];

    public function index()
    {
        $categories = Category::with('translations')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create', ['layoutOptions' => $this->layoutOptions]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|max:255|alpha_dash|unique:categories,slug',
            'name_ne' => 'required_without:name_en|nullable|string|max:255',
            'name_en' => 'required_without:name_ne|nullable|string|max:255',
            'layout_type' => 'required|in:' . implode(',', array_keys($this->layoutOptions)),
            'accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'required|string|max:50',
        ]);

        $category = Category::create([
            'slug' => Str::slug($data['slug']),
            'layout_type' => $data['layout_type'],
            'accent_color' => $data['accent_color'],
            'icon' => $data['icon'],
        ]);

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
        return view('admin.categories.edit', ['category' => $category, 'layoutOptions' => $this->layoutOptions]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name_ne' => 'required_without:name_en|nullable|string|max:255',
            'name_en' => 'required_without:name_ne|nullable|string|max:255',
            'layout_type' => 'required|in:' . implode(',', array_keys($this->layoutOptions)),
            'accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'required|string|max:50',
        ]);

        $category->update([
            'layout_type' => $data['layout_type'],
            'accent_color' => $data['accent_color'],
            'icon' => $data['icon'],
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