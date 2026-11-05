@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-line pb-4">
        <div>
            <div class="text-[11px] font-mono uppercase tracking-wider text-accent font-semibold mb-1">
                Akses Pengguna
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-ink">Manajemen Pengguna</h1>
            <p class="text-xs text-ink-muted mt-1">Kelola akun pengguna, hak akses administrator, dan status aktivitas.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-accent hover:bg-accent-hover text-white text-xs font-medium rounded transition shadow-xs self-start sm:self-auto">
            <x-icon name="plus" class="w-3.5 h-3.5 text-white" />
            <span>Tambah Pengguna</span>
        </a>
    </div>

    <div class="bg-surface border border-line rounded overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle border-b border-line text-ink-muted font-medium">
                    <tr>
                        <th class="py-3 px-4 w-12 font-mono">No</th>
                        <th class="py-3 px-4 min-w-[200px]">Nama Pengguna</th>
                        <th class="py-3 px-4 font-mono">Alamat Email</th>
                        <th class="py-3 px-4">Peran (Role)</th>
                        <th class="py-3 px-4 text-center">Riwayat SPK</th>
                        <th class="py-3 px-4 font-mono">Terdaftar Sejak</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-surface-subtle/50 transition">
                            <td class="py-3 px-4 font-mono text-ink-muted">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-surface-subtle border border-line flex items-center justify-center text-ink-muted shrink-0">
                                        <x-icon name="user" class="w-3.5 h-3.5" />
                                    </span>
                                    <div>
                                        <span class="font-medium text-ink">{{ $user->name }}</span>
                                        @if (auth()->id() === $user->id)
                                            <span class="text-[10px] text-accent ml-1 font-mono font-semibold">(Anda)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-ink-muted">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <x-badge :type="$user->role === 'admin' ? 'admin' : 'neutral'">
                                    {{ ucfirst($user->role) }}
                                </x-badge>
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-ink-muted">
                                {{ $user->spk_results_count }} sesi
                            </td>
                            <td class="py-3 px-4 font-mono text-ink-muted">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-subtle hover:bg-line border border-line rounded text-[11px] font-medium text-ink hover:text-accent transition">
                                        <x-icon name="edit" class="w-3 h-3 text-ink-muted" />
                                        <span>Ubah</span>
                                    </a>
                                    @if (auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Hapus akun {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded text-[11px] font-medium text-rose-700 hover:text-rose-900 transition">
                                                <x-icon name="trash" class="w-3 h-3 text-rose-600" />
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-ink-muted">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-4 py-3 border-t border-line bg-surface">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
