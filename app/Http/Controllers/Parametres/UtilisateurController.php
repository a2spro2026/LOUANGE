<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UtilisateurController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy('id')
            ->paginate(20);

        return view('parametres.utilisateurs', [
            'users' => $users,
            'statuts' => collect(User::STATUTS)->except('admin'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        User::create([
            'name' => $data['name'],
            'contact' => $data['contact'] ?? null,
            'login' => $data['login'],
            'statut' => $data['statut'],
            'password' => $data['password'],
            'email' => $data['login'].'@louange.local',
            'actif' => true,
        ]);

        return redirect()
            ->route('parametres.utilisateurs')
            ->with('success', 'Utilisateur ajouté.');
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $data = $this->validated($request, $utilisateur);

        $payload = [
            'name' => $data['name'],
            'contact' => $data['contact'] ?? null,
            'login' => $data['login'],
            'statut' => $data['statut'],
            'email' => $data['login'].'@louange.local',
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $utilisateur->update($payload);

        return redirect()
            ->route('parametres.utilisateurs')
            ->with('success', 'Utilisateur mis à jour.');
    }

    public function toggleActif(User $utilisateur): RedirectResponse
    {
        if ($utilisateur->id === auth()->id()) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas suspendre votre propre compte.']);
        }

        $utilisateur->actif = ! $utilisateur->actif;
        $utilisateur->save();

        $msg = $utilisateur->actif ? 'Utilisateur activé.' : 'Utilisateur suspendu.';

        return redirect()
            ->route('parametres.utilisateurs')
            ->with('success', $msg);
    }

    private function validated(Request $request, ?User $existing = null): array
    {
        $statuts = array_keys(collect(User::STATUTS)->except('admin')->all());
        if ($existing?->statut === 'admin') {
            $statuts[] = 'admin';
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:50'],
            'statut' => ['required', Rule::in($statuts)],
            'login' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'login')->ignore($existing?->id),
            ],
            'password' => [
                $existing ? 'nullable' : 'required',
                'string',
                'min:4',
                'max:100',
            ],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Ce login existe déjà.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 4 caractères.',
        ]);
    }
}
