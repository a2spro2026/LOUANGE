<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->input('annee', now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->year;
        }

        $mode = $request->input('periode', 'mois') === 'annee' ? 'annee' : 'mois';

        $chart = $mode === 'annee'
            ? $this->chartByYear()
            : $this->chartByMonth($year);

        return view('dashboard', [
            'chart' => $chart,
            'chartYear' => $year,
            'chartMode' => $mode,
            'availableYears' => $this->availableYears(),
        ]);
    }

    private function availableYears(): array
    {
        $years = BonAchat::query()
            ->selectRaw('YEAR(date_bon) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->map(fn ($y) => (int) $y)
            ->filter()
            ->values()
            ->all();

        $current = (int) now()->year;
        if (! in_array($current, $years, true)) {
            array_unshift($years, $current);
        }

        return $years ?: [$current];
    }

    private function chartByMonth(int $year): array
    {
        $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        $achatsRaw = BonAchat::query()
            ->selectRaw('MONTH(date_bon) as m, SUM(montant) as total')
            ->whereYear('date_bon', $year)
            ->groupBy(DB::raw('MONTH(date_bon)'))
            ->pluck('total', 'm');

        $achats = [];
        $ventes = [];
        $depenses = [];
        $charges = [];
        $benefices = [];

        for ($m = 1; $m <= 12; $m++) {
            $achat = round((float) ($achatsRaw[$m] ?? 0), 2);
            // Modules vente / dépenses / charges à brancher plus tard
            $vente = 0.0;
            $depense = 0.0;
            $charge = 0.0;
            $benefice = round($vente - $achat - $depense - $charge, 2);

            $achats[] = $achat;
            $ventes[] = $vente;
            $depenses[] = $depense;
            $charges[] = $charge;
            $benefices[] = $benefice;
        }

        return [
            'labels' => $labels,
            'achats' => $achats,
            'ventes' => $ventes,
            'depenses' => $depenses,
            'charges' => $charges,
            'benefices' => $benefices,
            'title' => 'Performance '.$year,
        ];
    }

    private function chartByYear(): array
    {
        $start = (int) now()->year - 4;
        $end = (int) now()->year;
        $labels = [];

        $achatsRaw = BonAchat::query()
            ->selectRaw('YEAR(date_bon) as y, SUM(montant) as total')
            ->whereYear('date_bon', '>=', $start)
            ->groupBy(DB::raw('YEAR(date_bon)'))
            ->pluck('total', 'y');

        $achats = [];
        $ventes = [];
        $depenses = [];
        $charges = [];
        $benefices = [];

        for ($y = $start; $y <= $end; $y++) {
            $labels[] = (string) $y;
            $achat = round((float) ($achatsRaw[$y] ?? 0), 2);
            $vente = 0.0;
            $depense = 0.0;
            $charge = 0.0;
            $benefice = round($vente - $achat - $depense - $charge, 2);

            $achats[] = $achat;
            $ventes[] = $vente;
            $depenses[] = $depense;
            $charges[] = $charge;
            $benefices[] = $benefice;
        }

        return [
            'labels' => $labels,
            'achats' => $achats,
            'ventes' => $ventes,
            'depenses' => $depenses,
            'charges' => $charges,
            'benefices' => $benefices,
            'title' => 'Performance '.$start.' – '.$end,
        ];
    }
}
