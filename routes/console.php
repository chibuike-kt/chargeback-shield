<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('shield:weekly-summary')
    ->weeklyOn(1, '8:00') // Every Monday at 8am
    ->timezone('Africa/Lagos');
