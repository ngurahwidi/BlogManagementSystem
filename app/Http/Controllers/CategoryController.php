<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    public function get(Request $request)
    {
        try {
            $category = ArticleCategory::all();

            $mappedCategory = $category->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'status' => $category->status,
                    'username' => $category->users->name
                ];
            });
            
            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Succes get data category',
                'data' => $mappedCategory
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 404,
                'message' => $th->getMessage(),
                'data' => []
            ], 404); 
        }
    }

    public function getById($id)
    {
        try {
            $category = ArticleCategory::findorfail($id);

            $mappedCategory = [
                'id' => $category->id,
                'name' => $category->name,
                'status' => $category->status,
                'username' => $category->users->name
            ];
            
            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Succes get data category',
                'data' => $mappedCategory
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 404,
                'message' => $th->getMessage(),
                'data' => []
            ], 404); 
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:article_categories',
                'status' => 'required|in:draft,publish',
                'userId' => 'required|exists:users,id'
            ]);

            if(Auth::user()->id != $request->userId){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to create an category',
                    'data' => []
                ]);
            }

            $category = ArticleCategory::create($request->only(['name', 'status', 'userId']));

            $mappedCategory = [
                'id' => $category->id,
                'name' => $category->name,
                'status' => $category->status,
                'username' => $category->users->name
            ];

            return response()->json([
                'status'=> 'Success',
                'code'=> 201,
                'message' => 'Category Created',
                'data' => $mappedCategory
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400); 
        }
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:article_categories',
                'status' => 'required|in:draft,publish',
                'userId' => 'required|exists:users,id'
            ]);

            $category = ArticleCategory::findorfail($id);

            if(Auth::user()->id != $category->userId){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to update an category',
                    'data' => []
                ]);
            }

            $category->fill($request->only(['name', 'status', 'userId']));

            $category->save();

            $mappedCategory = [
                'id' => $category->id,
                'name' => $category->name,
                'status' => $category->status,
                'username' => $category->users->name
            ];

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Category Updated',
                'data' => $mappedCategory
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400); 
        }
    }

    public function destroy($id)
    {
        try {
            $category = ArticleCategory::findorfail($id);

            if(Auth::user()->id != $category->userId){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to delete an category',
                    'data' => []
                ]);
            }

            $category->delete();

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Category Deleted',
                'data' => []
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400); 
        }
    }
}
