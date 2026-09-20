<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
$session = \App\Models\TutorSession::find(1);
if (!$session) {
    echo "Session 1 does not exist\n";
    exit;
}
$service = app(\App\Services\TutorAgentService::class);
try {
    echo $service->sendMessage($user, $session, 'hello');
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
