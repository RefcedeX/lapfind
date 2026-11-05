<?php

namespace App\Http\Controllers;

use App\Models\SpkResult;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HistoryController extends Controller
{
    /**
     * Display a listing of saved SPK evaluation history for the authenticated user.
     */
    public function index(): View
    {
        $results = SpkResult::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('history.index', compact('results'));
    }

    /**
     * Remove the specified evaluation result from storage.
     */
    public function destroy(SpkResult $spkResult): RedirectResponse
    {
        if ($spkResult->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak menghapus riwayat evaluasi ini.');
        }

        $title = $spkResult->title;
        $spkResult->delete();

        return redirect()
            ->route('history.index')
            ->with('success', "Riwayat \"{$title}\" berhasil dihapus.");
    }
}
