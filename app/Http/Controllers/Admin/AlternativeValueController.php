<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUpdateAlternativeValuesRequest;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Models\Criterion;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlternativeValueController extends Controller
{
    /**
     * Display a full decision matrix grid of alternatives across all criteria.
     */
    public function index(): View
    {
        $criteria = Criterion::orderBy('code')->get();
        $alternatives = Alternative::with('values')->orderBy('name')->get();

        // Buat mapping [alternative_id => [criteria_id => value]]
        $matrix = [];
        foreach ($alternatives as $alt) {
            $matrix[$alt->id] = [];
            foreach ($alt->values as $val) {
                $matrix[$alt->id][$val->criteria_id] = $val->value;
            }
        }

        return view('admin.values.index', compact('criteria', 'alternatives', 'matrix'));
    }

    /**
     * Update the decision matrix values in bulk.
     */
    public function update(BulkUpdateAlternativeValuesRequest $request): RedirectResponse
    {
        $submitted = $request->validated('values');

        DB::transaction(function () use ($submitted) {
            foreach ($submitted as $alternativeId => $criteriaValues) {
                foreach ($criteriaValues as $criteriaId => $val) {
                    AlternativeValue::updateOrCreate(
                        [
                            'alternative_id' => $alternativeId,
                            'criteria_id' => $criteriaId,
                        ],
                        [
                            'value' => (float) $val,
                        ]
                    );
                }
            }
        });

        AuditLogger::catalog(
            'matrix.bulk_update',
            'Admin memperbarui seluruh nilai matriks keputusan laptop secara massal ('.count($submitted).' unit).',
            ['total_alternatives_updated' => count($submitted)]
        );

        return redirect()
            ->route('admin.values.index')
            ->with('success', 'Matriks keputusan alternatif berhasil diperbarui secara keseluruhan.');
    }

    /**
     * Export the full decision matrix grid as a downloadable CSV.
     */
    public function export(): StreamedResponse
    {
        $criteria = Criterion::orderBy('code')->get();
        $alternatives = Alternative::with('values')->orderBy('name')->get();

        $filename = 'matriks-keputusan-spk-'.now()->format('Ymd_His').'.csv';

        AuditLogger::catalog('matrix.export', 'Admin mengekspor matriks keputusan kriteria ke format CSV.');

        return response()->streamDownload(function () use ($criteria, $alternatives) {
            $handle = fopen('php://output', 'w');

            // Header baris CSV
            $header = ['ID', 'Nama Laptop', 'Merek'];
            foreach ($criteria as $crit) {
                $header[] = "{$crit->name} ({$crit->code}) [{$crit->type}]";
            }
            fputcsv($handle, $header);

            // Data baris tiap laptop
            foreach ($alternatives as $alt) {
                $valMap = $alt->values->keyBy('criteria_id');
                $row = [$alt->id, $alt->name, $alt->brand];

                foreach ($criteria as $crit) {
                    if ($crit->code === 'C1' && ! isset($valMap[$crit->id])) {
                        $row[] = $alt->price;
                    } else {
                        $row[] = isset($valMap[$crit->id]) ? $valMap[$crit->id]->value : 0;
                    }
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
