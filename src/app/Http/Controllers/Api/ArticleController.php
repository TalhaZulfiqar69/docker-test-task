<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    //
    public function articlesListing(Request $request) {
        try {
            $id = $request->input('userId');
            $perPage = $request->input('per_page');
            if ($id) {
                $articles = Article::whereHas('userPreferences', function ($query) use($id) {
                    $query->where('userId', $id);
                })->paginate($perPage);

            } else {
                $perPage = $request->input('per_page');
                // Fetch articles with pagination
                $articles = Article::paginate($perPage);        
            }
            if ($articles) {
                return response()->json([
                    'message' => 'Articles fetched successfully.',
                    'success' => true,
                    'data' => $articles,
                ], 201);
            }
        } 
        catch (\Exception $e) {
            // Handle the exception, log it, and return an appropriate error response.
            return response()->json([
                'message' => $e,
                'success' => false,
                'data' => null,
            ], 500);
        }
    }

    public function findArticle($id) {
        try {
            $article = Article::find($id);

            if ($article) {
                return response()->json([
                    'message' => 'Article fetched successfully.',
                    'success' => true,
                    'data' => $article,
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Article not exists with the provided id.',
                    'success' => true,
                    'data' => $article,
                ], 201);
            }
        } catch (\Exception $e) {
            // Handle the exception, log it, and return an appropriate error response.
            return response()->json([
                'message' => $e,
                'success' => false,
                'data' => null,
            ], 500);
        }
    }

    public function prefs() {
        try {
            $preferences = UserPreference::all();
            if (!$preferences) {
                // ...
            } else {
                dd($preferences);
                // ...
            }
        } catch (\Exception $e) {
            // Handle the exception, log it, and return an appropriate error response.
            return response()->json([
                'message' => $e,
                'success' => false,
                'data' => null,
            ], 500);
        }
    }

    public function updatePreferences(Request $request, $id) {
        try {

            $validator = Validator::make($request->all(), [
                'userId' => 'required|integer',
                'preference' => 'required|string',
                // Add any other validation rules as needed
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'success' => false,
                    'data' => null,
                ], 404);
            } else {
                $userPreference = new UserPreference();
                $userPreference['userId'] = $id;
                $userPreference['preference'] = $request['preference'];
                $userPreference->save();
                // dd($request->all(), $id, $user);
                return response()->json([
                    'message' => 'Preferences updated successfully',
                    'success' => true,
                    'data' => $user->preferences, // Optionally, return the updated preferences
                ], 201);
            }
        } catch (\Exception $e) {
            // Handle the exception, log it, and return an appropriate error response.
            return response()->json([
                'message' => $e,
                'success' => false,
                'data' => null,
            ], 500);
        }
    }
}
