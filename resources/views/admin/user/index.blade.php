<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 text-xs font-bold text-gray-500 uppercase">Nama</th>
                            <th class="p-4 text-xs font-bold text-gray-500 uppercase">Email</th>
                            <th class="p-4 text-xs font-bold text-gray-500 uppercase">Role</th>
                            <th class="p-4 text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 text-sm font-semibold">{{ $user->name }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="p-4">
                                <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="text-xs border-gray-200 rounded-lg">
                                        <option value="admin" {{ $user->isRole('admin') ? 'selected' : '' }}>Admin</option>
                                        <option value="pegawai" {{ $user->isRole('pegawai') ? 'selected' : '' }}>Pegawai</option>
                                    </select>
                                </form>
                            </td>
                            <td class="p-4">
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs font-bold uppercase">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
