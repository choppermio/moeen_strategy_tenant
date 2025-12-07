<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display organization selection page
     */
    public function select()
    {
        $user = auth()->user();
        $organizations = $user->organizations()->active()->get();

        if ($organizations->count() === 0) {
            return redirect()->route('no-organization');
        }

        if ($organizations->count() === 1) {
            // Auto-switch if only one organization
            $user->switchOrganization($organizations->first()->id);
            return redirect()->intended('/');
        }

        return view('organizations.select', compact('organizations'));
    }

    /**
     * Switch to a different organization
     */
    public function switch(Request $request)
    {
        \Log::info('Organization switch called', [
            'organization_id' => $request->organization_id,
            'user_id' => auth()->id(),
            'all_input' => $request->all(),
        ]);
        
        $request->validate([
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);

        $user = auth()->user();
        $organizationId = (int) $request->organization_id;

        \Log::info('Before belongsToOrganization check', [
            'org_id' => $organizationId,
            'is_admin' => $user->isSystemAdmin(),
        ]);

        if (!$user->belongsToOrganization($organizationId) && !$user->isSystemAdmin()) {
            \Log::warning('User does not belong to organization', [
                'user_id' => $user->id,
                'org_id' => $organizationId,
            ]);
            return back()->with('error', 'ليس لديك صلاحية الوصول لهذه المنظمة.');
        }

        // Update directly in database
        $updated = \DB::table('users')->where('id', $user->id)->update([
            'current_organization_id' => $organizationId,
            'updated_at' => now(),
        ]);
        
        \Log::info('Database update result', [
            'updated' => $updated,
            'user_id' => $user->id,
            'org_id' => $organizationId,
        ]);
        
        // Also set in session
        $request->session()->put('current_organization_id', $organizationId);
        $request->session()->save();
        
        $organization = Organization::find($organizationId);

        return redirect('/')
            ->with('success', 'تم التبديل إلى منظمة: ' . $organization->name);
    }

    /**
     * Show no organization page
     */
    public function noOrganization()
    {
        return view('organizations.no-organization');
    }

    /**
     * List all organizations (admin only)
     */
    public function index()
    {
        $organizations = Organization::withCount('users')->paginate(15);
        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show create organization form (admin only)
     */
    public function create()
    {
        return view('organizations.create');
    }

    /**
     * Store a new organization
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'slug', 'description']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organizations', 'public');
        }

        $organization = Organization::create($data);

        // Add current user as admin of the new organization
        $organization->users()->attach(auth()->id(), [
            'role' => 'admin',
            'is_default' => false,
        ]);

        return redirect()->route('organizations.index')
            ->with('success', 'تم إنشاء المنظمة بنجاح: ' . $organization->name);
    }

    /**
     * Show organization details
     */
    public function show(Organization $organization)
    {
        $organization->load('users');
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show edit organization form
     */
    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update an organization
     */
    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug,' . $organization->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'slug', 'description']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organizations', 'public');
        }

        $organization->update($data);

        return redirect()->route('organizations.index')
            ->with('success', 'تم تحديث المنظمة بنجاح: ' . $organization->name);
    }

    /**
     * Delete an organization
     */
    public function destroy(Organization $organization)
    {
        $name = $organization->name;
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'تم حذف المنظمة: ' . $name);
    }

    /**
     * Manage organization users
     */
    public function users(Organization $organization)
    {
        $organization->load('users');
        $allUsers = \App\Models\User::whereDoesntHave('organizations', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })->get();

        return view('organizations.users', compact('organization', 'allUsers'));
    }

    /**
     * Add user to organization
     */
    public function addUser(Request $request, Organization $organization)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|in:admin,manager,member',
        ]);

        // Check if user already belongs to organization
        if ($organization->users()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'هذا المستخدم مضاف مسبقاً للمنظمة.');
        }

        $organization->users()->attach($request->user_id, [
            'role' => $request->role,
            'is_default' => false,
        ]);

        return back()->with('success', 'تمت إضافة المستخدم للمنظمة بنجاح.');
    }

    /**
     * Remove user from organization
     */
    public function removeUser(Request $request, Organization $organization)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $organization->users()->detach($request->user_id);

        // If this was the user's current organization, clear it
        $user = \App\Models\User::find($request->user_id);
        if ($user && $user->current_organization_id === $organization->id) {
            $user->current_organization_id = null;
            $user->save();
        }

        return back()->with('success', 'تمت إزالة المستخدم من المنظمة.');
    }

    /**
     * Update user role in organization
     */
    public function updateUserRole(Request $request, Organization $organization)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|in:admin,manager,member',
        ]);

        $organization->users()->updateExistingPivot($request->user_id, [
            'role' => $request->role,
        ]);

        return back()->with('success', 'تم تحديث دور المستخدم بنجاح.');
    }

    /**
     * Get current organization info via AJAX
     */
    public function current()
    {
        $user = auth()->user();
        $organization = $user->currentOrganization;

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد منظمة محددة.',
            ]);
        }

        return response()->json([
            'success' => true,
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'logo' => $organization->logo,
            ],
            'role' => $user->roleInOrganization($organization->id),
        ]);
    }

    /**
     * Get list of user's organizations via AJAX
     */
    public function list()
    {
        $user = auth()->user();
        $organizations = $user->organizations()->active()->get();

        return response()->json([
            'success' => true,
            'organizations' => $organizations->map(function ($org) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                    'logo' => $org->logo,
                    'role' => $org->pivot->role,
                    'is_default' => $org->pivot->is_default,
                ];
            }),
            'current_id' => $user->current_organization_id,
        ]);
    }

    /**
     * Switch organization via AJAX
     */
    public function switchAjax(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);

        $user = auth()->user();
        $organizationId = (int) $request->organization_id;

        if (!$user->belongsToOrganization($organizationId)) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الوصول لهذه المنظمة.',
            ], 403);
        }

        // Update user's current organization in database
        \DB::table('users')->where('id', $user->id)->update([
            'current_organization_id' => $organizationId,
            'updated_at' => now(),
        ]);
        
        // Set session using request
        $request->session()->put('current_organization_id', $organizationId);
        $request->session()->save();
        
        \Log::info('Organization switched', [
            'user_id' => $user->id,
            'org_id' => $organizationId,
            'session_id' => session()->getId(),
        ]);
        
        $organization = Organization::find($organizationId);

        return response()->json([
            'success' => true,
            'message' => 'تم التبديل إلى منظمة: ' . $organization->name,
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'logo' => $organization->logo,
            ],
        ]);
    }
}
