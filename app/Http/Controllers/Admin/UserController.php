<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Helpers\ActivityLogger;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['roles', 'country', 'city']);
            
            // Apply filters
            if ($request->filled('role')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }
            
            if ($request->filled('country_id')) {
                $query->where('country_id', $request->country_id);
            }
            
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }
            
            if ($request->filled('verified')) {
                if ($request->verified == '1') {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            }
            
            return DataTables::of($query)
                ->addColumn('role', function($user) {
                    return $user->roles->pluck('name')->implode(', ');
                })
                ->addColumn('country', function($user) {
                    return $user->country ? $user->country->name_en : '-';
                })
                ->addColumn('city', function($user) {
                    return $user->city ? $user->city->name_en : '-';
                })
                ->addColumn('status', function($user) {
                    return $user->is_active 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('verified', function($user) {
                    return $user->email_verified_at 
                        ? '<span class="badge bg-success"><i class="fas fa-check"></i> Verified</span>' 
                        : '<span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>';
                })
                ->addColumn('action', function($user) {
                    $actions = '
                        <div class="btn-group" role="group">
                            <a href="'.route('admin.users.show', $user->id).'" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="'.route('admin.users.edit', $user->id).'" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="'.route('admin.users.destroy', $user->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\')">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                    return $actions;
                })
                ->rawColumns(['status', 'verified', 'action'])
                ->make(true);
        }
        
        $countries = Country::all();
        $cities = City::all();
        
        return view('admin.users.index', compact('countries', 'cities'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $countries = Country::all();
        return view('admin.users.create', compact('countries'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'email_verified_at' => $request->verified ? now() : null,
        ]);
        
        $user->assignRole($request->role);
        
        // Log activity
        ActivityLogger::log('created_user', "Created user: {$user->name}", $user);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with(['roles', 'country', 'city', 'products'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        $user = User::with(['roles', 'country', 'city'])->findOrFail($id);
        $countries = Country::all();
        return view('admin.users.edit', compact('user', 'countries'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'email_verified_at' => $request->verified ? now() : null,
        ]);
        
        $user->syncRoles([$request->role]);
        
        // Log activity
        ActivityLogger::log('updated_user', "Updated user: {$user->name}", $user);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete yourself.');
        }
        
        $userName = $user->name;
        $user->delete();
        
        // Log activity
        ActivityLogger::log('deleted_user', "Deleted user: {$userName}");
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    /**
     * Change user password.
     */
    public function changePassword(Request $request, string $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->back()
            ->with('success', 'Password changed successfully.');
    }
    
    /**
     * Send email to user.
     */
    public function sendEmail(Request $request, string $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        $user = User::findOrFail($id);
        
        // Send email logic here
        // Mail::to($user->email)->send(new CustomEmail($request->subject, $request->message));
        
        return redirect()->back()
            ->with('success', 'Email sent successfully.');
    }
    
    /**
     * Export users to CSV/Excel.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        
        $users = User::with(['roles', 'country', 'city'])->get();
        
        $filename = 'users_' . date('Y-m-d_His') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Country',
                'City',
                'Role',
                'Status',
                'Email Verified',
                'Created At',
            ]);
            
            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->country ? $user->country->name_en : '',
                    $user->city ? $user->city->name_en : '',
                    $user->roles->pluck('name')->implode(', '),
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get user products.
     */
    public function products(string $id)
    {
        $user = User::with('products')->findOrFail($id);
        return view('admin.users.products', compact('user'));
    }
}
