<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $subCategoryId     = $request->query('sub_category');
        $filterSubCategory = null;

        if ($subCategoryId) {
            $filterSubCategory = SubCategory::with('category')
                ->where('is_active', true)
                ->findOrFail($subCategoryId);

            // Only load the parent category with the filtered subcategory and its services
            $categories = Category::where('id', $filterSubCategory->category_id)
                ->with(['subCategories' => fn($q) => $q
                    ->where('id', $subCategoryId)
                    ->with(['services' => fn($sq) => $sq->where('is_active', true)->orderBy('name')])
                ])
                ->get();
        } else {
            $categories = Category::where('is_active', true)
                ->orderBy('name')
                ->with(['subCategories' => fn($q) => $q
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->with(['services' => fn($sq) => $sq->where('is_active', true)->orderBy('name')])
                ])
                ->get();
        }

        return view('services.index', compact('categories', 'filterSubCategory'));
    }
}
