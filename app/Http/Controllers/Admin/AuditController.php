<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $events = OrderEvent::query()
            ->with(['order', 'actor'])
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($eventQuery) use ($search): void {
                    $eventQuery->where('event_type', 'like', '%'.$search.'%')
                        ->orWhereHas('order', function ($orderQuery) use ($search): void {
                            $orderQuery->where('order_number', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.audit.index', compact('events'));
    }
}
