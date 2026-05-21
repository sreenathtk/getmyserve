<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\StaffAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::where('role', User::ROLE_STAFF)
            ->with('staffAssignments')
            ->orderBy('name')
            ->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->with(['subCategories' => function ($q) {
                $q->where('is_active', true)->with(['services' => function ($q) {
                    $q->where('is_active', true)->orderBy('name');
                }])->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.staff.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => User::ROLE_STAFF,
                'is_active' => true,
            ]);

            $this->syncAssignments($user, $request);
        });

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        $categories = Category::where('is_active', true)
            ->with(['subCategories' => function ($q) {
                $q->where('is_active', true)->with(['services' => function ($q) {
                    $q->where('is_active', true)->orderBy('name');
                }])->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Build lookup: category_id → ['all' => bool, 'service_ids' => []]
        $assignments = $staff->staffAssignments()->get();
        $assignmentMap = [];
        foreach ($assignments as $a) {
            $catId = $a->category_id;
            if ($a->service_id === null) {
                $assignmentMap[$catId]['all'] = true;
            } else {
                $assignmentMap[$catId]['all'] = $assignmentMap[$catId]['all'] ?? false;
                $assignmentMap[$catId]['service_ids'][] = $a->service_id;
            }
        }

        return view('admin.staff.edit', compact('staff', 'categories', 'assignmentMap'));
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', Password::min(8)];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $staff) {
            $data = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $staff->update($data);

            $this->syncAssignments($staff, $request);
        });

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function toggleStatus(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        $staff->update(['is_active' => !$staff->is_active]);

        $status = $staff->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member {$status} successfully.");
    }

    // -------------------------------------------------------------------------

    private function syncAssignments(User $user, Request $request): void
    {
        // Delete existing assignments and rebuild from form input
        $user->staffAssignments()->delete();

        $selectedCategories = $request->input('categories', []);
        $allServicesFor     = $request->input('all_services', []);
        $serviceSelections  = $request->input('services', []);

        foreach ($selectedCategories as $categoryId) {
            if (in_array($categoryId, $allServicesFor)) {
                // null service_id = full access to the entire category
                StaffAssignment::create([
                    'user_id'     => $user->id,
                    'category_id' => $categoryId,
                    'service_id'  => null,
                ]);
            } else {
                $serviceIds = $serviceSelections[$categoryId] ?? [];
                foreach ($serviceIds as $serviceId) {
                    StaffAssignment::create([
                        'user_id'     => $user->id,
                        'category_id' => $categoryId,
                        'service_id'  => $serviceId,
                    ]);
                }
            }
        }
    }
}
