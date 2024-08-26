<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function get(Request $request)
    {
        try {
            $tag = Tag::all();

            $mappedTag = $tag->map(function ($tag){
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'status' => $tag->status,
                    'username' => $tag->users->name
                ];
            });

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Succes get data tag',
                'data' => $mappedTag
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

    public function getById($id)
    {
        try {
            $tag = Tag::findorfail($id);

            $mappedTag = [
                'id' => $tag->id,
                'name' => $tag->name,
                'status' => $tag->status,
                'username' => $tag->users->name
            ];

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Succes get data tag',
                'data' => $mappedTag
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:tags',
                'status' => 'required|in:draft,publish',
                'userId' => 'required|exists:users,id'
            ]);

             if(Auth::user()->id != $request->userId){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to create an tag',
                    'data' => []
                ], 403);
            }

            $tag = Tag::create($request->only(['name', 'status', 'userId']));

            $mappedTag = [
                'id' => $tag->id,
                'name' => $tag->name,
                'status' => $tag->status,
                'username' => $tag->users->name
            ];

            return response()->json([
                'status'=> 'Success',
                'code'=> 201,
                'message' => 'Tag Created',
                'data' => $mappedTag
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

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:tags',
                'status' => 'required|in:draft,publish',
                'userId' => 'required|exists:users,id'
            ]);

            $tag = Tag::findorfail($id);

            if(Auth::user()->id != $tag->userId){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to update an tag',
                    'data' => []
                ], 403);
            }

            $tag->update($request->only(['name', 'status', 'userId']));

            $mappedTag = [
                'id' => $tag->id,
                'name' => $tag->name,
                'status' => $tag->status,
                'username' => $tag->users->name
            ];

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Tag Updated',
                'data' => $mappedTag
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
            $tag = Tag::findorfail($id);

            if(Auth::user()->id != $tag->userId){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to delete an tag',
                    'data' => []
                ], 403);
            }

            $tag->delete();

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Success Delete Data',
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
