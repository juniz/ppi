<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lengkapi Profil - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.18),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.18),_transparent_28%)]"></div>
        <div class="relative mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-12">
            <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-slate-200/70 lg:grid-cols-[1.1fr,0.9fr]">
                <section class="bg-slate-950 px-8 py-10 text-white sm:px-10">
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Login Google</p>
                    <h1 class="mt-5 text-3xl font-semibold leading-tight sm:text-4xl">Lengkapi data akun sebelum masuk ke dashboard.</h1>
                    <p class="mt-4 max-w-md text-sm leading-7 text-slate-300 sm:text-base">
                        Kami sudah menerima data dasar dari Google. Tinggal cocokkan NIK pegawai dan ruangan kerja agar akun bisa dipakai di aplikasi.
                    </p>

                    <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Akun Google</p>
                        <dl class="mt-4 space-y-4">
                            <div>
                                <dt class="text-sm text-slate-400">Nama</dt>
                                <dd class="mt-1 text-lg font-medium">{{ $socialUser['name'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-400">Email</dt>
                                <dd class="mt-1 text-lg font-medium break-all">{{ $socialUser['email'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <section class="px-8 py-10 sm:px-10">
                    <div class="max-w-xl">
                        <h2 class="text-2xl font-semibold text-slate-900">Data pegawai</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Setelah disimpan, akun akan dibuat atau diperbarui lalu Anda langsung masuk ke halaman utama.
                        </p>

                        @if ($errors->any())
                            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                <ul class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('socialite.complete-profile.store') }}" class="mt-8 space-y-6">
                            @csrf

                            <div>
                                <label for="nip" class="block text-sm font-medium text-slate-700">NIK</label>
                                <input
                                    id="nip"
                                    name="nip"
                                    type="text"
                                    value="{{ old('nip', $socialUser['nip']) }}"
                                    placeholder="Masukkan NIK pegawai"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    required
                                >
                                <p class="mt-2 text-xs text-slate-500">NIK akan divalidasi ke data pegawai yang sudah ada di sistem.</p>
                            </div>

                            <div>
                                <label for="id_ruang" class="block text-sm font-medium text-slate-700">Ruang PPI</label>
                                <select
                                    id="id_ruang"
                                    name="id_ruang"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                                    required
                                >
                                    <option value="">Pilih ruangan</option>
                                    @foreach ($ruanganOptions as $kode => $nama)
                                        <option value="{{ $kode }}" @selected(old('id_ruang', $socialUser['id_ruang'] ?? null) == $kode)>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                                >
                                    Simpan dan masuk
                                </button>
                                <a
                                    href="{{ route('filament.admin.auth.login') }}"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Kembali ke login
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
