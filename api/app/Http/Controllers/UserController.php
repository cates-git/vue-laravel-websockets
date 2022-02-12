<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $search = '%'.$request->search.'%';
        $data = User::where('id', '!=', $request->user()->id)
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                })
                ->orderBy('name')->limit(20)->get();

        return response()->json([
            'status'  => true,
            'message' => 'Search List',
            'data'    => $data
        ]);
    }

    public function show($contact_id)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Contact Info',
            'data'    => User::find($contact_id)
        ]);
    }
}
