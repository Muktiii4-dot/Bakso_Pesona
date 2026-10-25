<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'sort' => ['nullable', 'in:latest,oldest,name'],
        ]);

        $query = User::query()
            ->where('role', 'customer')
            ->withCount('orders');

        if (! empty($validated['search'])) {
            $term = trim($validated['search']);
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        match ($validated['sort'] ?? 'latest') {
            'oldest' => $query->oldest(),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $customers = $query->paginate(12)->withQueryString();
        $totalCustomers = User::where('role', 'customer')->count();

        return view('admin.customers.index', compact('customers', 'totalCustomers'));
    }
}
