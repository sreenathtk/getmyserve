<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use Illuminate\Http\Request;

class CallLogController extends Controller
{
    public function index(Request $request)
    {
        $query = CallLog::with(['agent', 'recording'])->latest('started_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('caller_number', 'like', "%{$request->search}%")
                  ->orWhere('callee_number', 'like', "%{$request->search}%")
                  ->orWhere('caller_name',   'like', "%{$request->search}%")
                  ->orWhere('callee_name',   'like', "%{$request->search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $callLogs = $query->paginate(50)->withQueryString();

        $agents = \App\Models\CallAgent::where('is_active', true)->get();

        return view('admin.calls.index', compact('callLogs', 'agents'));
    }

    public function show(CallLog $callLog)
    {
        $callLog->load([
            'agent',
            'recording',
            'notes.user',
            'transfers.fromAgent',
            'transfers.toAgent',
            'communicationHistory.links.linkable',
        ]);

        return view('admin.calls.show', compact('callLog'));
    }
}
