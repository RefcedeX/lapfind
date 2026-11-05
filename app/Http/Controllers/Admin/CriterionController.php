<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCriterionRequest;
use App\Http\Requests\UpdateCriterionRequest;
use App\Models\Criterion;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CriterionController extends Controller
{
    /**
     * Display a listing of criteria.
     */
    public function index(): View
    {
        $criteria = Criterion::withCount('alternativeValues')
            ->orderBy('code')
            ->get();

        return view('admin.criteria.index', compact('criteria'));
    }

    /**
     * Show the form for creating a new criterion.
     */
    public function create(): View
    {
        return view('admin.criteria.create');
    }

    /**
     * Store a newly created criterion in storage.
     */
    public function store(StoreCriterionRequest $request): RedirectResponse
    {
        $criterion = Criterion::create($request->validated());

        AuditLogger::criterion(
            action: 'criterion.create',
            description: "Menambahkan kriteria baru: {$criterion->code} ({$criterion->name}) jenis ".strtoupper($criterion->type),
            properties: ['code' => $criterion->code, 'name' => $criterion->name, 'type' => $criterion->type, 'unit' => $criterion->unit]
        );

        return redirect()
            ->route('admin.criteria.index')
            ->with('success', "Kriteria {$criterion->code} ({$criterion->name}) berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified criterion.
     */
    public function edit(Criterion $criterion): View
    {
        return view('admin.criteria.edit', compact('criterion'));
    }

    /**
     * Update the specified criterion in storage.
     */
    public function update(UpdateCriterionRequest $request, Criterion $criterion): RedirectResponse
    {
        $criterion->update($request->validated());

        AuditLogger::criterion(
            action: 'criterion.update',
            description: "Memperbarui kriteria: {$criterion->code} ({$criterion->name})",
            properties: ['code' => $criterion->code, 'changes' => $criterion->getChanges()]
        );

        return redirect()
            ->route('admin.criteria.index')
            ->with('success', "Kriteria {$criterion->code} berhasil diperbarui.");
    }

    /**
     * Remove the specified criterion from storage.
     */
    public function destroy(Criterion $criterion): RedirectResponse
    {
        $code = $criterion->code;
        $name = $criterion->name;

        $criterion->delete();

        AuditLogger::criterion(
            action: 'criterion.delete',
            description: "Menghapus kriteria: {$code} ({$name})",
            properties: ['code' => $code, 'name' => $name]
        );

        return redirect()
            ->route('admin.criteria.index')
            ->with('success', "Kriteria {$code} ({$name}) berhasil dihapus beserta data matriks terkait.");
    }
}
