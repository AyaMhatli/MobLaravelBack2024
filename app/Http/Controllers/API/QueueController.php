<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Queue;
use App\Models\Departement;
use App\Models\User;


class QueueController extends Controller
{
    
 /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function AppelTraites()
   { 



       
       $user = Auth::user();
       if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }
    
        $today = Carbon::today();
       $departmentId = $user->department_id;
      // $count = Queue::where('called', 0)->count();
      $count = Queue::where('department_id', $departmentId)
                      ->whereDate('created_at', $today)
                      ->where('called', 1)->count();
   
       // Retourner le nombre de files d'attente au format JSON
       return response()->json(['count' => $count-1]);
   }

    public function AppelNONTraites()
{
   
    $user = Auth::user();
    if (!$user) {
     return response()->json(['error' => 'User not authenticated'], 401);
 }
       $today = Carbon::now();
       $departmentId = $user->department_id;
       // $count = Queue::where('called', 0)->count();
       $count = Queue::where('department_id', $departmentId)
       ->whereDate('created_at', $today)->where('called', 0)->count();
  // \Log::info('Queues: ' . $queues->count());
 
   return response()->json(['count' => $count]);
}
public function numeroActuelle()
{ 
    $today = Carbon::today();
   
   // $count = Queue::where('called', 0)->count();
   $count = Queue::whereDate('created_at', $today)->where('called', 1)->count();

    // Retourner le nombre de files d'attente au format JSON
    return response()->json(['count' => $count]);
}
public function traiterQueue()
{    
    $user = Auth::user();
    if (!$user) {
     return response()->json(['error' => 'User not authenticated'], 401);
 }
    // Retrieve the current date
    $currentDate = Carbon::now()->toDateString();
    $departmentId = $user->department_id;
    // Retrieve the oldest unprocessed queue item created today
    $queueItem = Queue::where('department_id', $departmentId)
                        ->whereDate('created_at', $currentDate)
                       ->where('called', 0)
                       ->orderBy('created_at', 'asc')
                       ->first();

    if ($queueItem) {
        // Mark this item as processed
        $queueItem->called = 1;
        $queueItem->save();

        // Retrieve the next unprocessed queue item
      /*  $nextQueueItem = Queue::whereDate('created_at', $currentDate)
                              ->where('called', 0)
                              ->orderBy('created_at', 'asc')
                              ->first();*/

        return response()->json(['message' => 'Queue item processed', /*'next_queue_item' => $nextQueueItem*/]);
    }

    return response()->json(['message' => 'No queue items to process'], 200);
}

}

  /* 
  
    public function show(Queue $queue)
    {
        // Retourner une ressource pour une file d'attente spécifique
        return new QueueResource($queue);
    }

    public function store(QueueRequest $request)
    {
        // Créer une nouvelle file d'attente
        $queue = Queue::create($request->all());

        // Retourner la ressource de la file d'attente nouvellement créée
        return new QueueResource($queue);
    }

    public function update(QueueRequest $request, Queue $queue)
    {
        // Mettre à jour la file d'attente
        $queue->update($request->all());

        // Retourner la ressource mise à jour de la file d'attente
        return new QueueResource($queue);
    }

   public function destroy(Queue $queue)
    {
        // Supprimer la file d'attente
        $queue->delete();

        // Retourner une réponse JSON
        return response()->json(null, 204);
    }*/




