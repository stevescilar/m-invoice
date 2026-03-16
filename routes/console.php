<?php

use App\Jobs\SendInvoiceReminders;
use App\Jobs\MarkOverdueInvoices;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendInvoiceReminders)->everyFiveMinutes();
Schedule::job(new MarkOverdueInvoices)->dailyAt('00:01');
Schedule::command('invoices:process-recurring')->dailyAt('06:00');
Schedule::command('documents:mark-stale')->dailyAt('07:00');
