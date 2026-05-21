<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ziwo\InitiateCallRequest;
use App\Http\Requests\Ziwo\AddCallNoteRequest;
use App\Models\CallAgent;
use App\Models\CallLog;
use App\Models\CallNote;
use App\Models\CommunicationLink;
use App\Services\Ziwo\ZiwoAgentService;
use App\Services\Ziwo\ZiwoCallService;
use App\Services\Ziwo\ZiwoRecordingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CallController extends Controller
{
    public function __construct(
        private ZiwoCallService      $callService,
        private ZiwoAgentService     $agentService,
        private ZiwoRecordingService $recordingService,
    ) {}

    /**
     * Initiate an outbound call from the admin panel.
     */
    public function dial(InitiateCallRequest $request): JsonResponse
    {
        $agent = CallAgent::where('user_id', auth()->id())->firstOrFail();

        $result = $this->callService->dial(
            toNumber:  $request->phone,
            agentId:   $agent->ziwo_agent_id,
            callerId:  config('ziwo.default_caller_id'),
        );

        // Store link context so webhook listener can attach call to entity
        if (! empty($result['callId']) && $request->filled('entity_type')) {
            $morphMap = [
                'order'              => \App\Models\Order::class,
                'enquiry'            => \App\Models\Enquiry::class,
                'assistance_request' => \App\Models\AssistanceRequest::class,
                'customer'           => \App\Models\User::class,
                'provider'           => \App\Models\ServiceProvider::class,
            ];

            if (isset($morphMap[$request->entity_type])) {
                Cache::put("ziwo_call_context:{$result['callId']}", [
                    'linkable_type' => $morphMap[$request->entity_type],
                    'linkable_id'   => $request->entity_id,
                    'initiated_by'  => auth()->id(),
                ], now()->addHours(4));
            }
        }

        return response()->json([
            'success' => true,
            'callId'  => $result['callId'] ?? null,
            'message' => 'Call initiated successfully.',
        ]);
    }

    /**
     * Generate a fresh ZIWO agent token for the browser softphone.
     */
    public function agentToken(): JsonResponse
    {
        $agent = CallAgent::where('user_id', auth()->id())->firstOrFail();
        $token = $this->agentService->getAgentToken($agent->ziwo_agent_id);

        return response()->json(['token' => $token]);
    }

    /**
     * Get live call stats for the dashboard widget.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'active_calls' => \App\Models\ActiveCall::with('agent')->get(),
            'agents'       => CallAgent::where('is_active', true)->get(),
            'missed_today' => CallLog::missed()->today()->count(),
            'calls_today'  => CallLog::today()->count(),
        ]);
    }

    /**
     * Manually link a call to a business entity (Order, Enquiry, etc.).
     */
    public function linkCall(Request $request, CallLog $callLog): JsonResponse
    {
        $request->validate([
            'linkable_type' => 'required|string|in:order,enquiry,assistance_request,customer,provider',
            'linkable_id'   => 'required|integer',
        ]);

        $morphMap = [
            'order'              => \App\Models\Order::class,
            'enquiry'            => \App\Models\Enquiry::class,
            'assistance_request' => \App\Models\AssistanceRequest::class,
            'customer'           => \App\Models\User::class,
            'provider'           => \App\Models\ServiceProvider::class,
        ];

        $history = $callLog->communicationHistory;

        if (! $history) {
            return response()->json(['error' => 'No communication history for this call yet.'], 422);
        }

        CommunicationLink::firstOrCreate([
            'communication_history_id' => $history->id,
            'linkable_type'            => $morphMap[$request->linkable_type],
            'linkable_id'              => $request->linkable_id,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Add a note to a call log.
     */
    public function addNote(AddCallNoteRequest $request, CallLog $callLog): JsonResponse
    {
        $note = CallNote::create([
            'call_log_id' => $callLog->id,
            'user_id'     => auth()->id(),
            'note'        => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'note'    => [
                'id'         => $note->id,
                'note'       => $note->note,
                'created_by' => auth()->user()->name,
                'created_at' => $note->created_at->format('d M Y H:i'),
            ],
        ]);
    }

    /**
     * Return a temporary URL to stream a call recording.
     */
    public function recordingUrl(CallLog $callLog): JsonResponse
    {
        $recording = $callLog->recording;

        if (! $recording) {
            return response()->json(['error' => 'Recording not available.'], 404);
        }

        return response()->json([
            'url'      => $this->recordingService->getPlaybackUrl($recording),
            'duration' => $recording->duration_seconds,
            'format'   => $recording->format,
        ]);
    }

    /**
     * Stream a locally-stored recording file (proxied — no direct file URL).
     */
    public function streamRecording(CallLog $callLog)
    {
        $recording = $callLog->recording;

        abort_if(! $recording || ! $recording->is_downloaded, 404, 'Recording not downloaded yet.');

        $path = \Illuminate\Support\Facades\Storage::disk($recording->storage_disk)
                                                    ->path($recording->storage_path);

        abort_if(! file_exists($path), 404, 'Recording file not found.');

        return response()->file($path, ['Content-Type' => 'audio/mpeg', 'Cache-Control' => 'no-store']);
    }
}
