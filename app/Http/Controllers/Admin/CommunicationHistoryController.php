<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Services\Ziwo\CommunicationTimelineService;
use Illuminate\Http\Request;

class CommunicationHistoryController extends Controller
{
    private array $morphMap = [
        'order'              => Order::class,
        'enquiry'            => Enquiry::class,
        'assistance_request' => AssistanceRequest::class,
        'customer'           => User::class,
        'provider'           => ServiceProvider::class,
    ];

    public function __construct(private CommunicationTimelineService $timeline) {}

    public function timeline(string $type, int $id)
    {
        abort_unless(array_key_exists($type, $this->morphMap), 404);

        $modelClass = $this->morphMap[$type];
        $entity     = $modelClass::findOrFail($id);
        $history    = $this->timeline->forEntity($modelClass, $id);

        return view('admin.communication.timeline', compact('entity', 'history', 'type'));
    }

    public function addNote(Request $request, string $type, int $id)
    {
        abort_unless(array_key_exists($type, $this->morphMap), 404);

        $request->validate(['note' => 'required|string|max:2000']);

        $this->timeline->addNote(
            modelClass: $this->morphMap[$type],
            id:         $id,
            note:       $request->note,
            userId:     auth()->id(),
        );

        return back()->with('success', 'Note added to timeline.');
    }
}
