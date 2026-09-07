<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('category')->latest()->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'title.ne'      => 'required_without:title.en|nullable|string|max:255',
            'title.en'      => 'required_without:title.ne|nullable|string|max:255',
            'excerpt.ne'    => 'nullable|string|max:500',
            'excerpt.en'    => 'nullable|string|max:500',
            'body.ne'       => 'required_without:body.en|nullable|string',
            'body.en'       => 'required_without:body.ne|nullable|string',
            'image'         => 'nullable|image|max:2048',
            'is_published'  => 'boolean',
            'published_at'  => 'nullable|date',
        ]);

        // Slug prefers the Nepali title, but falls back to English
        // if the admin only filled in the English tab.
        $titleForSlug = $data['title']['ne'] ?? $data['title']['en'];
        $data['slug'] = Str::slug($titleForSlug).'-'.uniqid();
        $data['user_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Drop empty English strings so getTranslation() falls back to 'ne'
        // cleanly instead of storing an empty string as the 'en' value.
        foreach (['title', 'excerpt', 'body'] as $field) {
            if (empty($data[$field]['en'])) {
                unset($data[$field]['en']);
            }
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'News created.');
    }

    public function edit(News $news)
    {
        $categories = Category::all();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'title.ne'      => 'required_without:title.en|nullable|string|max:255',
            'title.en'      => 'required_without:title.ne|nullable|string|max:255',
            'excerpt.ne'    => 'nullable|string|max:500',
            'excerpt.en'    => 'nullable|string|max:500',
            'body.ne'       => 'required_without:body.en|nullable|string',
            'body.en'       => 'required_without:body.ne|nullable|string',
            'image'         => 'nullable|image|max:2048',
            'is_published'  => 'boolean',
            'published_at'  => 'nullable|date',
        ]);

        $data['is_published'] = $request->boolean('is_published');

        foreach (['title', 'excerpt', 'body'] as $field) {
            if (empty($data[$field]['en'])) {
                unset($data[$field]['en']);
            }
        }

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'News updated.');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();

        return back()->with('success', 'News deleted.');
    }
}