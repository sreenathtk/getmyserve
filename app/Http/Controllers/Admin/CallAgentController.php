<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallAgent;
use App\Services\Ziwo\ZiwoAgentService;
use Illuminate\Http\Request;

class CallAgentController extends Controller
{
    public function __construct(private ZiwoAgentService $agentService) {}

    public function index()
    {
        $agents = CallAgent::with('user')->orderBy('display_name')->get();
        return view('admin.call-agents.index', compact('agents'));
    }

    public function sync()
    {
        $count = $this->agentService->syncAgents();
        return back()->with('success', "Synced {$count} agents from ZIWO.");
    }

    public function update(Request $request, CallAgent $agent)
    {
        $request->validate([
            'user_id'   => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $agent->update($request->only(['user_id', 'is_active']));

        return back()->with('success', 'Agent updated.');
    }
}
