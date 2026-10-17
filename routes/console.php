<?php

use App\Jobs\SendInvoiceReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendInvoiceReminders)->everyFiveMinutes();
Schedule::command('invoices:process-recurring')->dailyAt('06:00');
Schedule::command('documents:mark-stale')->dailyAt('07:00');
