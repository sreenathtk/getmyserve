<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WatiTemplate;
use App\Services\WatiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WatiTemplateController extends Controller
{
    public function index(): View
    {
        $templates    = WatiTemplate::orderBy('recipient_type')->orderBy('template_key')->get();
        $lastSyncedAt = $templates->whereNotNull('last_synced_at')->max('last_synced_at');

        return view('admin.whatsapp.templates.index', compact('templates', 'lastSyncedAt'));
    }

    public function sync(WatiService $wati): RedirectResponse
    {
        $watiTemplates = $wati->getTemplates();

        if (empty($watiTemplates)) {
            return redirect()->route('admin.whatsapp.templates.index')
                ->with('error', 'Could not connect to WATI API. Check your API credentials in .env and try again.');
        }

        $synced  = 0;
        $missing = 0;
        $now     = now();

        foreach (WatiTemplate::all() as $template) {
            $watiData = $watiTemplates[$template->element_name] ?? null;

            if ($watiData === null) {
                $template->update([
                    'status'           => null,
                    'body'             => null,
                    'category'         => null,
                    'language'         => null,
                    'rejection_reason' => null,
                    'last_synced_at'   => $now,
                ]);
                $missing++;
                continue;
            }

            $template->update([
                'status'           => $watiData['status'] ?? null,
                'body'             => $watiData['body'] ?? null,
                'category'         => $watiData['category'] ?? null,
                'language'         => $watiData['language'] ?? $template->language,
                'rejection_reason' => $watiData['rejectedReason'] ?? null,
                'last_synced_at'   => $now,
            ]);
            $synced++;
        }

        $message = "Sync complete — {$synced} template(s) updated from WATI.";
        if ($missing > 0) {
            $message .= " {$missing} template(s) were not found in your WATI account.";
        }

        return redirect()->route('admin.whatsapp.templates.index')->with('success', $message);
    }
}
