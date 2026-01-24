<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'admin@inovasisehat.com')->first();
if ($user && Hash::check('InovasiSehat2026', $user->password)) {
    echo "LOGIN SUCCEEDED\n";
} else {
    echo "LOGIN FAILED\n";
}
