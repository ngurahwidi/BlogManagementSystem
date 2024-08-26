<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
  
    public function get(Request $request){
        try {
            $query = Article::search($request)->paginate(10);

            if($query->isEmpty()){
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 404,
                    'message' => 'Data article not found',
                    'data' => []
                ], 404);
            }

            $mappedArticle = $query->map(function ($article){
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'user_name' => $article->users->name,
                    'user_email' => $article->users->email,
                    'categories' => $article->categories->pluck('name'),
                    'tags' => $article->tags->pluck('name'),
                    'status' => $article->status,
                    'created_at' => $article->created_at,
                    'file_path' => $article->file_path,
                ];
            });

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Succes get data article',
                'data' => $mappedArticle
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

    public function getById($id){
        try {
            $article = Article::with('categories')->findorfail($id);

            $mappedArticle =
                 [
                    'id' => $article->id,
                    'title' => $article->title,
                    'user_name' => $article->users->name,
                    'user_email' => $article->users->email,
                    'content' => $article->content,
                    'file_path' => $article->file_path,
                    'categories' => $article->categories->pluck('name'),
                    'tags' => $article->tags->pluck('name'),
                    'status' => $article->status,
                    'updated_at' => $article->updated_at
                ];

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Data article succes get by id',
                'data' => $mappedArticle
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

    public function store(Request $request){
        try {
            $request->validate ([
                'title' => 'required|string|max:100',
                'userId' => 'required|integer',
                'content' => 'required|string',
                'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'category_ids' => 'required|array',
                'category_ids.*' => 'exists:article_categories,id',
                'tag_ids' => 'required|array',
                'tag_ids.*' => 'exists:tags,id',
                'status' => 'required|string'
            ]);

            if(Auth::user()->id != $request->input('userId')) {
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 403,
                    'message' => 'You are not authorized to create an article',
                    'data' => []
                ], 403);
            }

            $article = Article::create($request->only(['title', 'userId', 'content', 'status']));
            
           

            if($request->hasFile('file')) {
                $file = $request->file('file');
                $title = $request->input('title');
                $sanitizedTitle = str_replace(' ', '_', $title);
                $lowerTitle = strtolower($sanitizedTitle);
                $extension = $file->getClientOriginalExtension();
                $filename = $lowerTitle.'.'.$extension;
                $filePath = $file->storeAs('uploads/article', $filename, 'public');

                $article->file_path = '/storage/'. $filePath;
            }

            $article->save();

            $article->categories()->attach($request->input('category_ids'));
            $article->tags()->attach($request->input('tag_ids'));

            foreach($article->categories as $category) {
                if($category->status === 'draft') {
                    return response()->json([
                        'status'=> 'Error',
                        'code'=> 403,
                        'message' => 'You are not authorized to create an article, article category must be publish',
                        'data' => []
                    ], 403);
                }
            }

            foreach($article->tags as $tag) {
                if($tag->status === 'draft') {
                    return response()->json([
                        'status'=> 'Error',
                        'code'=> 403,
                        'message' => 'You are not authorized to create an article, article tag must be publish',
                        'data' => []
                    ], 403);
                }
            }

            $mappedArticle =
            [
                'id' => $article->id,
                'title' => $article->title,
                'user_name' => $article->users->name,
                'user_email' => $article->users->email,
                'file_path' => $article->file_path,
                'categories' => $article->categories->pluck('name'),
                'tags' => $article->tags->pluck('name'),
                'created_at' => $article->created_at
            ];


            return response()->json([
                'status'=> 'Success',
                'code'=> 201,
                'message' => 'Success Create Data',
                'data' => $mappedArticle
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

    public function update(Request $request, $id){
        try {   
           
        $request->validate ([
            'title' => 'required|string|max:100',
            'userId' => 'required|integer',
            'content' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'category_ids' => 'required|array',
            'tag_ids' => 'required|array',
            'status' => 'required|string'
        ]);

        $article = Article::findorfail($id);

        if(Auth::user()->id != $article->userId) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 403,
                'message' => 'You are not authorized to update an article',
                'data' => []
            ], 403);
        }

        $article->update($request->only(['title', 'userId', 'content']));

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $title = $request->input('title');
            $sanitizedTitle = str_replace(' ', '_', $title);
            $lowerTitle = strtolower($sanitizedTitle);
            $extension = $file->getClientOriginalExtension();
            $filename = $lowerTitle.'.'.$extension;
            $filePath = $file->storeAs('uploads/article', $filename, 'public');

            $article->file_path = '/storage/'. $filePath;
        }

        $article->save();

        $article->categories()->sync($request->input('category_ids'));
        $article->tags()->sync($request->input('tag_ids'));

        $mappedArticle =
        [
            'id' => $article->id,
            'title' => $article->title,
            'user_name' => $article->users->name,
            'user_email' => $article->users->email,
            'file_path' => $article->file_path,
            'categories' => $article->categories->pluck('name'),
            'tags' => $article->tags->pluck('name'),
            'updated_at' => $article->updated_at
        ];

        return response()->json([
            'status'=> 'Success',
            'code'=> 200,
            'message' => 'Updated Data Success',
            'data' => $mappedArticle
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

    public function destroy($id){
        try {
            $article = Article::findorfail($id);

            if(Auth::user()->id != $article->userId) {
               return response()->json([
                   'status'=> 'Error',
                   'code'=> 403,
                   'message' => 'You are not authorized to delete an article',
                   'data' => []
               ]); 
            }

            $article->categories()->detach();

            $article->delete();

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
};