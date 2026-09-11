<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(15);
        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.ads.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'required|image|max:2048',
            'link'       => 'nullable|url',
            'position'   => 'required|in:sidebar,footer,between_news',
            'is_active'  => 'boolean',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data['image'] = $request->file('image')->store('ads', 'public');
        $data['is_active'] = $request->boolean('is_active');

        Ad::create($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad created successfully.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'nullable|image|max:2048',
            'link'       => 'nullable|url',
            'position'   => 'required|in:sidebar,footer,between_news',
            'is_active'  => 'boolean',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($request->hasFile('image')) {
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $ad->update($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad updated successfully.');
    }

    public function destroy(Ad $ad)
    {
        Storage::disk('public')->delete($ad->image);
        $ad->delete();

        return back()->with('success', 'Ad deleted.');
    }
}