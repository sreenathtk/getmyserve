<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $activeCategoryId = $request->query('category');

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->with(['subCategories' => fn($q) => $q
                ->where('is_active', true)
                ->orderBy('name')
            ])
            ->get();

        // Resolve which tab should be active on initial load
        $activeCategory = $activeCategoryId
            ? $categories->firstWhere('id', $activeCategoryId)
            : null;

        return view('categories.index', compact('categories', 'activeCategory'));
    }
}
