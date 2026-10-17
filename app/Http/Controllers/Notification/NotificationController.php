<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $company = Auth::user()->company;
        $filter  = $request->get('filter', 'all');

        $query = Notification::where('company_id', $company->id)->latest();

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif (in_array($filter, ['quotation_approved', 'quotation_rejected', 'payment_submitted'])) {
            $query->where('type', $filter);
        }

        $notifications = $query->paginate(20);
        $unreadCount   = Notification::where('company_id', $company->id)->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    public function open(Notification $notification): RedirectResponse
    {
        if ($notification->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $notification->markAsRead();

        if (!empty($notification->action_url)) {
            return redirect($notification->action_url);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllRead(): RedirectResponse
    {
        Notification::where('company_id', Auth::user()->company_id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        if ($notification->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification removed.');
    }
}
