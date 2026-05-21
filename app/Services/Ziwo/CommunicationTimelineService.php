<?php

namespace App\Services\Ziwo;

use App\Models\CommunicationHistory;
use App\Models\CommunicationLink;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommunicationTimelineService
{
    public function forEntity(string $modelClass, int $id, int $perPage = 20): LengthAwarePaginator
    {
        return CommunicationHistory::whereHas('links', function ($q) use ($modelClass, $id) {
            $q->where('linkable_type', $modelClass)
              ->where('linkable_id', $id);
        })
        ->with(['reference', 'agent', 'user'])
        ->orderByDesc('happened_at')
        ->paginate($perPage);
    }

    public function addNote(string $modelClass, int $id, string $note, int $userId): CommunicationHistory
    {
        $history = CommunicationHistory::create([
            'channel'     => 'note',
            'direction'   => 'internal',
            'summary'     => 'Note added',
            'body'        => $note,
            'user_id'     => $userId,
            'happened_at' => now(),
        ]);

        CommunicationLink::create([
            'communication_history_id' => $history->id,
            'linkable_type'            => $modelClass,
            'linkable_id'              => $id,
        ]);

        return $history;
    }
}
