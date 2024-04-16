<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['categories'] = Category::orderBy('id', 'desc')->get();

        return view('categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return redirect()->route('categories.index');
        }

        $requested_data = $request->all();
        $requested_data['uuid'] = substr(uniqid(), -6);

        $data['category'] = Category::create();

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['category'] = Category::find($id);

        return view('categories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return redirect()->route('categories.index');
        }

        $requested_data = $request->only('name', 'time');
        $requested_data['synchronized'] = 0;

        $category = Category::find($id);

        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        $category->update($requested_data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    public function syncCategories(Request $request)
    {
        $data['category'] = Category::updateOrCreate(
            [
                'uuid' => $request->uuid
            ],
            $request->all()
        );

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }
}
