<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
    public function index(Category $category)
    {
        $subCategories = $category->subCategories()->withCount('services')->orderBy('name')->get();
        return view('admin.sub-categories.index', compact('category', 'subCategories'));
    }

    public function create(Category $category)
    {
        return view('admin.sub-categories.create', compact('category'));
    }

    public function store(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:sub_categories,name,NULL,id,category_id,' . $category->id,
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon'        => 'nullable|string|max:100',
        ]);

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon ?: null,
            'is_active'   => true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/sub-categories', 'public');
        }

        $category->subCategories()->create($data);

        return redirect()->route('admin.categories.sub-categories.index', $category)
            ->with('success', 'Sub-category created successfully.');
    }

    public function edit(Category $category, SubCategory $subCategory)
    {
        return view('admin.sub-categories.edit', compact('category', 'subCategory'));
    }

    public function update(Request $request, Category $category, SubCategory $subCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:sub_categories,name,' . $subCategory->id . ',id,category_id,' . $category->id,
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon'        => 'nullable|string|max:100',
        ]);

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon ?: null,
        ];

        if ($request->hasFile('image')) {
            if ($subCategory->image) {
                Storage::disk('public')->delete($subCategory->image);
            }
            $data['image'] = $request->file('image')->store('images/sub-categories', 'public');
        } elseif ($request->boolean('remove_image') && $subCategory->image) {
            Storage::disk('public')->delete($subCategory->image);
            $data['image'] = null;
        }

        $subCategory->update($data);

        return redirect()->route('admin.categories.sub-categories.index', $category)
            ->with('success', 'Sub-category updated successfully.');
    }

    public function toggleStatus(Category $category, SubCategory $subCategory)
    {
        $subCategory->update(['is_active' => !$subCategory->is_active]);

        $status = $subCategory->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.categories.sub-categories.index', $category)
            ->with('success', "Sub-category {$status} successfully.");
    }
}
