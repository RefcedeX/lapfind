<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlternativeRequest;
use App\Http\Requests\UpdateAlternativeRequest;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Models\Criterion;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlternativeController extends Controller
{
    /**
     * Display a listing of laptop alternatives with filter and search.
     */
    public function index(Request $request): View
    {
        $query = Alternative::with('values.criterion');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        $laptops = $query->orderBy('name')->paginate(10)->withQueryString();
        $brands = Alternative::select('brand')->distinct()->orderBy('brand')->pluck('brand');
        $criteria = Criterion::orderBy('code')->get();

        return view('admin.alternatives.index', compact('laptops', 'brands', 'criteria'));
    }

    /**
     * Show the form for creating a new laptop alternative.
     */
    public function create(): View
    {
        $criteria = Criterion::orderBy('code')->get();

        return view('admin.alternatives.create', compact('criteria'));
    }

    /**
     * Store a newly created laptop alternative and its criteria values.
     */
    public function store(StoreAlternativeRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'values']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('laptops', 'public');
        }

        $laptop = Alternative::create($data);

        AuditLogger::catalog(
            action: 'alternative.create',
            description: "Menambahkan unit laptop baru: \"{$laptop->name}\" ({$laptop->brand}) seharga Rp ".number_format($laptop->price, 0, ',', '.'),
            properties: ['alternative_id' => $laptop->id, 'name' => $laptop->name, 'brand' => $laptop->brand, 'price' => $laptop->price]
        );

        // Simpan nilai kriteria jika disertakan
        $values = $request->input('values', []);
        foreach ($values as $criteriaId => $value) {
            if ($value !== null && $value !== '') {
                AlternativeValue::create([
                    'alternative_id' => $laptop->id,
                    'criteria_id' => $criteriaId,
                    'value' => (float) $value,
                ]);
            }
        }

        return redirect()
            ->route('admin.alternatives.index')
            ->with('success', "Laptop \"{$laptop->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified laptop alternative.
     */
    public function show(Alternative $alternative): View
    {
        $alternative->load('values.criterion');
        $criteria = Criterion::orderBy('code')->get();

        return view('admin.alternatives.show', compact('alternative', 'criteria'));
    }

    /**
     * Show the form for editing the specified laptop alternative.
     */
    public function edit(Alternative $alternative): View
    {
        $alternative->load('values');
        $criteria = Criterion::orderBy('code')->get();
        $valuesMap = $alternative->values->pluck('value', 'criteria_id')->toArray();

        return view('admin.alternatives.edit', compact('alternative', 'criteria', 'valuesMap'));
    }

    /**
     * Update the specified laptop alternative and its criteria values.
     */
    public function update(UpdateAlternativeRequest $request, Alternative $alternative): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'values']);

        if ($request->hasFile('image')) {
            if ($alternative->image && Storage::disk('public')->exists($alternative->image)) {
                Storage::disk('public')->delete($alternative->image);
            }
            $data['image'] = $request->file('image')->store('laptops', 'public');
        }

        $alternative->update($data);

        AuditLogger::catalog(
            action: 'alternative.update',
            description: "Memperbarui data laptop: \"{$alternative->name}\"",
            properties: ['alternative_id' => $alternative->id, 'name' => $alternative->name, 'changes' => $alternative->getChanges()]
        );

        // Update nilai kriteria
        $values = $request->input('values', []);
        foreach ($values as $criteriaId => $value) {
            if ($value !== null && $value !== '') {
                AlternativeValue::updateOrCreate(
                    [
                        'alternative_id' => $alternative->id,
                        'criteria_id' => $criteriaId,
                    ],
                    [
                        'value' => (float) $value,
                    ]
                );
            }
        }

        return redirect()
            ->route('admin.alternatives.index')
            ->with('success', "Data laptop \"{$alternative->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified laptop alternative from storage.
     */
    public function destroy(Alternative $alternative): RedirectResponse
    {
        $name = $alternative->name;

        if ($alternative->image && Storage::disk('public')->exists($alternative->image)) {
            Storage::disk('public')->delete($alternative->image);
        }

        $alternative->delete();

        AuditLogger::catalog(
            action: 'alternative.delete',
            description: "Menghapus data laptop: \"{$name}\"",
            properties: ['name' => $name]
        );

        return redirect()
            ->route('admin.alternatives.index')
            ->with('success', "Laptop \"{$name}\" berhasil dihapus.");
    }

    /**
     * Export all laptop catalog alternatives as a downloadable CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $criteria = Criterion::orderBy('code')->get();
        $query = Alternative::with('values.criterion');

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        $laptops = $query->orderBy('name')->get();
        $filename = 'katalog-laptop-spk-'.now()->format('Ymd_His').'.csv';

        AuditLogger::catalog('alternatives.export', 'Admin mengekspor data katalog laptop ke format CSV.');

        return response()->streamDownload(function () use ($criteria, $laptops) {
            $handle = fopen('php://output', 'w');

            // Header baris CSV
            $header = ['ID', 'Nama Laptop', 'Merek', 'Harga (Rp)'];
            foreach ($criteria as $crit) {
                if ($crit->code !== 'C1') {
                    $header[] = "{$crit->name} ({$crit->unit})";
                }
            }
            $header[] = 'Deskripsi';
            $header[] = 'Didaftarkan Pada';
            fputcsv($handle, $header);

            // Data baris laptop
            foreach ($laptops as $laptop) {
                $valMap = $laptop->values->keyBy(fn ($v) => $v->criterion->code);
                $row = [$laptop->id, $laptop->name, $laptop->brand, $laptop->price];

                foreach ($criteria as $crit) {
                    if ($crit->code !== 'C1') {
                        $row[] = isset($valMap[$crit->code]) ? $valMap[$crit->code]->value : '-';
                    }
                }

                $row[] = $laptop->description;
                $row[] = $laptop->created_at?->format('Y-m-d H:i:s') ?? '-';

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
