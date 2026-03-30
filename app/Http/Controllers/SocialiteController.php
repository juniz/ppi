<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RuangAuditKepatuhan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return $this->socialiteDriver($request, $provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $response = $this->socialiteDriver($request, $provider)->user();
        } catch (InvalidStateException $exception) {
            return redirect()
                ->route('filament.admin.auth.login')
                ->withErrors([
                    'google' => 'Sesi login Google tidak cocok. Coba login lagi dari host yang sama, misalnya tetap gunakan localhost atau tetap gunakan 127.0.0.1.',
                ]);
        }

        if (blank($response->getEmail())) {
            return redirect()
                ->route('filament.admin.auth.login')
                ->withErrors([
                    'google' => 'Akun Google ini tidak mengirim alamat email. Gunakan akun lain atau hubungi admin.',
                ]);
        }

        $user = User::query()
            ->where('email', $response->getEmail())
            ->orWhere('google_id', $response->getId())
            ->first();

        if ($user && $user->hasCompleteGoogleProfile()) {
            $user->update([
                $provider . '_id' => $response->getId(),
                'name' => $user->name ?: ($response->getName() ?: $response->getNickname() ?: 'Pengguna Google'),
                'email' => $response->getEmail(),
            ]);

            Auth::login($user, remember: true);

            $request->session()->forget('socialite.google_user');

            return redirect()->intended(route('filament.admin.pages.dashboard'));
        }

        $request->session()->put('socialite.google_user', [
            'provider' => $provider,
            'id' => $response->getId(),
            'name' => $response->getName() ?: $response->getNickname() ?: 'Pengguna Google',
            'email' => $response->getEmail(),
            'user_id' => $user?->id,
            'nip' => $user?->nip,
            'id_ruang' => $user?->id_ruang,
        ]);

        return redirect()->route('socialite.complete-profile.show');
    }

    protected function socialiteDriver(Request $request, string $provider)
    {
        return Socialite::driver($provider)
            ->redirectUrl($request->getSchemeAndHttpHost() . '/auth/' . $provider . '/callback');
    }

    public function showCompleteProfile(Request $request)
    {
        $socialUser = $request->session()->get('socialite.google_user');

        abort_unless($socialUser, 404);

        return View::make('auth.socialite.complete-profile', [
            'socialUser' => $socialUser,
            'ruanganOptions' => RuangAuditKepatuhan::query()
                ->orderBy('nama_ruang')
                ->pluck('nama_ruang', 'id_ruang'),
        ]);
    }

    public function storeCompleteProfile(Request $request): RedirectResponse
    {
        $socialUser = $request->session()->get('socialite.google_user');

        abort_unless($socialUser, 404);

        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:20', 'exists:pegawai,nik'],
            'id_ruang' => ['required', 'string', 'max:5', 'exists:ruang_audit_kepatuhan,id_ruang'],
        ], [
            'nip.exists' => 'NIK belum terdaftar di data pegawai.',
            'id_ruang.exists' => 'Ruangan yang dipilih tidak valid.',
        ]);

        $pegawai = Pegawai::query()->find($validated['nip']);

        $user = User::query()
            ->when($socialUser['user_id'] ?? null, fn ($query, $userId) => $query->orWhere('id', $userId))
            ->orWhere('email', $socialUser['email'])
            ->orWhere('google_id', $socialUser['id'])
            ->first();

        $payload = [
            'google_id' => $socialUser['id'],
            'name' => $pegawai?->nama ?: $socialUser['name'],
            'email' => $socialUser['email'],
            'nip' => $validated['nip'],
            'id_ruang' => $validated['id_ruang'],
        ];

        if ($user) {
            $user->update($payload);
        } else {
            $payload['password'] = Str::random(32);
            $user = User::create($payload);
        }

        Auth::login($user, remember: true);

        $request->session()->forget('socialite.google_user');

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }

    protected function validateProvider(string $provider): array
    {
        return $this->getValidationFactory()->make(
            ['provider' => $provider],
            ['provider' => 'in:google']
        )->validate();
    }
}
