<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{

    public function index() {
        $categories = Category::latest()->pagination(10);

        return view ('admin.category.list');
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->input('name');
            $category->slug = $request->input('slug');
            $category->status = $request->input('status');
            $category->save();

            Session::flash('success', 'La catégorie a été ajoutée');

            return response()->json([
                'status' => true,
                'message' => 'La catégorie a été ajoutée',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    // ... autres méthodes du contrôleur ...
}
