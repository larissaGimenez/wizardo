<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\WheelManager;
use App\Livewire\SpellManager;
use App\Livewire\QuestManager;
use App\Livewire\ChallengeManager;
use App\Livewire\WheelDetails;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/wheels', WheelManager::class)->name('wheel.manager');
Route::get('/wheels/{wheel}', WheelDetails::class)->name('wheel.details');
Route::get('/spells', SpellManager::class)->name('spell.manager');
Route::get('/quests', QuestManager::class)->name('quest.manager');
Route::get('/challenges', ChallengeManager::class)->name('challenge.manager');
