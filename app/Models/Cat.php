<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cat extends Model
{
    protected $fillable = ['name', 'hunger', 'energy', 'hygiene', 'happiness', 'is_sick', 'last_interacted_at'];
    protected $casts = ['last_interacted_at' => 'datetime', 'is_sick' => 'boolean'];

    public function checkSickness()
    {
        $zeroCount = 0;
        
        if ($this->hunger <= 0) $zeroCount++;
        if ($this->energy <= 0) $zeroCount++;
        if ($this->hygiene <= 0) $zeroCount++;
        if ($this->happiness <= 0) $zeroCount++;

        // If 2 or more stats hit exactly 0, trigger sick mode
        if ($zeroCount >= 2) {
            $this->is_sick = true;
        }
    }

    public function calculateDecay()
    {
        $now = Carbon::now();
        $minutesPassed = $this->last_interacted_at->diffInMinutes($now);

        if ($minutesPassed > 0) {
            $this->hunger = max(0, $this->hunger - floor($minutesPassed / 10));
            $this->hygiene = max(0, $this->hygiene - floor($minutesPassed / 15));
            $this->happiness = max(0, $this->happiness - floor($minutesPassed / 20));
            
            $this->checkSickness();

            if ($this->is_sick) {
                $this->energy = max(0, $this->energy - floor($minutesPassed / 5));
            }
            
            $this->last_interacted_at = $now;
            $this->save();
        }
    }
}