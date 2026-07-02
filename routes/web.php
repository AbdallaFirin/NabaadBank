<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Staff (Admin) Routes ──────────────────────────────────────────────────────
require __DIR__.'/admin.php';

// ── Customer Portal Routes ────────────────────────────────────────────────────
require __DIR__.'/customer.php';
