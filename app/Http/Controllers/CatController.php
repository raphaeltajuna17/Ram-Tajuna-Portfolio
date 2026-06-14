<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cat;

class CatController extends Controller
{
    public function index(Request $request)
    {
        $catId = session('cat_id');
        $cat = $catId ? Cat::find($catId) : null;

        // If they don't have a cat session, send them to the main welcome screen
        if (!$cat) { 
            return view('adopt');
        }

        $cat->refresh();
        $cat->calculateDecay();

        return view('dashboard', compact('cat'));
    }

    // NEW: Method to handle the adoption form
    public function adopt(Request $request)
    {
        $request->validate(['name' => 'required|string|max:20']);
        
        $cat = Cat::create(['name' => $request->input('name')]);
        session(['cat_id' => $cat->id]);

        return redirect('/');
    }

    public function interact(Request $request)
    {
        $catId = session('cat_id');
        $cat = $catId ? Cat::find($catId) : null;

        if (!$cat) return response()->json(['error' => 'No active cat'], 404);

        $action = $request->input('action');

        // Handle Start New Game
        if ($action === 'reset') {
            $cat->delete(); // Delete the current cat from the database
            session()->forget('cat_id'); // Wipe the user's browser memory
            return response()->json(['redirect' => url('/')]); // Tell the frontend to reload the page
        }
        
        // Handle Renaming
        if ($action === 'rename') {
            $request->validate(['name' => 'required|string|max:20']);
            $cat->name = $request->input('name');
        } 
        // Block playing and feeding if sick
        elseif ($cat->is_sick && in_array($action, ['feed', 'play'])) {
            // Do nothing
        } 
        // Standard Game Actions
        else {
            if ($action === 'feed') {
                $cat->hunger = min(100, $cat->hunger + 30);
                $cat->hygiene = max(0, $cat->hygiene - 10);
            } elseif ($action === 'clean') {
                $cat->hygiene = min(100, $cat->hygiene + 50);
            } elseif ($action === 'play') {
                $cat->happiness = min(100, $cat->happiness + 40);
                $cat->energy = max(0, $cat->energy - 20);
                $cat->hunger = max(0, $cat->hunger - 20);
                $cat->hygiene = max(0, $cat->hygiene - 20);
            } elseif ($action === 'sleep') {
                $cat->energy = min(100, $cat->energy + 50);
                $cat->hunger = max(0, $cat->hunger - 10);
            } elseif ($action === 'medicine') {
                $cat->is_sick = false;
                $cat->hunger = min(100, $cat->hunger + 40);
                $cat->energy = min(100, $cat->energy + 40);
                $cat->hygiene = min(100, $cat->hygiene + 40);
                $cat->happiness = min(100, $cat->happiness + 40);
            }
            
            $cat->checkSickness();
        }

        $cat->last_interacted_at = now();
        $cat->save();
        $cat->age_in_days = intval($cat->created_at->diffInDays(now()));

        return response()->json($cat);
    }
}