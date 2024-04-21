<?php

use App\Livewire\TasksComponent;
use App\Livewire\TaskTable;
use Illuminate\Support\Facades\Route;

Route::get('/', TasksComponent::class);
Route::get('/table', TaskTable::class);