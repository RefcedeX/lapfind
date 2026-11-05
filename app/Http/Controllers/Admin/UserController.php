<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::withCount('spkResults')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        AuditLogger::log(
            action: 'user.create',
            category: 'user',
            description: "Menambahkan akun pengguna baru: \"{$user->name}\" ({$user->email}) dengan peran ".strtoupper($user->role),
            properties: ['user_id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil ditambahkan dengan peran {$user->role}.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['password']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        AuditLogger::log(
            action: 'user.update',
            category: 'user',
            description: "Memperbarui data pengguna: \"{$user->name}\" ({$user->email})",
            properties: ['user_id' => $user->id, 'changes' => $user->getChanges()]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Data pengguna \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Cegah admin menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $name = $user->name;
        $email = $user->email;
        $user->delete();

        AuditLogger::log(
            action: 'user.delete',
            category: 'user',
            description: "Menghapus akun pengguna: \"{$name}\" ({$email})",
            properties: ['name' => $name, 'email' => $email]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna \"{$name}\" berhasil dihapus.");
    }
}
