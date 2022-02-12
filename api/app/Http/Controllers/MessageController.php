<?php

namespace App\Http\Controllers;

use App\Events\Message as EventsMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user_id = $request->user()->id;
    //     $data = Message::from('messages as m')
    //                 ->with(['user_from', 'user_to'])
    //                 ->select('*', DB::raw("(CASE user_id_from WHEN ".$user_id." THEN true ELSE false END) as sent_by_me"))
    //                 ->where(function($query) use ($user_id) {
    //                     $query->where(function($query) use ($user_id) {
    //                         $query->where('user_id_from', $user_id)
    //                             ->orWhere('user_id_to', $user_id);
    //                     })
    //                     ->whereNotIn('id', function($query) use ($user_id) {
    //                         $query->select('id')
    //                             ->from(with(new Message)->getTable())
    //                             ->where('user_id_from', $user_id)
    //                             ->orWhere(DB::raw('user_id_to = m.user_id_to'));
    //                     });
    //                 })
    //                 ->orWhere(function($query) use ($user_id) {
    //                     $query->where('user_id_from', $user_id)
    //                         ->where('user_id_to', $user_id);
    //                 })
    //                 ->groupBy('user_id_from')
    //                 ->orderBy('created_at', 'DESC')->limit(10)->get();

    //     $messages = [];
    //     foreach ($data as $message) {
    //         $message->messages = [];
    //         $messages[$message->user_id_from] = $message;
    //     }

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'List',
    //         'data'    => $messages
    //     ]);
    // }


    public function index(Request $request)
    {
        $user_id = $request->user()->id;

        $q1 = Message::from(with(new Message)->getTable() .' as a')
            ->select('user_id_to as id', 'created_at')
            ->where('user_id_from', $user_id);

        $q2 = Message::from(with(new Message)->getTable() .' as b')
            ->select('user_id_from as id', 'created_at')
            ->where('user_id_to', $user_id)
            ->union($q1);

        $q3 = DB::table($q2, 'c')
            // ->groupBy('id')
            // ->groupBy('created_at')
            ->orderByDesc('created_at');

       $q4 = DB::table($q3, 'd')
            ->select('id')
            ->groupBy('id')
            ->limit(10)
            ->get();

        $contact_ids = Arr::pluck($q4, 'id');

        // $data = User::whereIn('id', function($query) use ($q2) {
        //         $query->select('id')
        //             ->from($q2, 'c')
        //             ->orderByDesc('created_at')
        //             ->limit(10);
        //         })
        //     ->limit(2)
        //     ->get();

        //     return $data;

        $data = User::whereIn('id', $contact_ids)->get();
        $contacts = [];
        foreach ($data as $contact) {
            $contacts[$contact->id] = $contact;
        }

        $order = [];
        foreach ($contact_ids as $contact_id) {
            $c = $contacts[$contact_id];
            $c['messages'] = Message::select('*',
                        'created_at as created_datetime',
                        DB::raw("(CASE user_id_from WHEN ".$user_id." THEN true ELSE false END) as sent_by_me"
                    ))
                    ->where(function($query) use ($user_id, $contact_id) {
                    $query->where('user_id_from', $user_id)
                        ->where('user_id_to', $contact_id);
                })
                ->orWhere(function($query) use ($user_id, $contact_id) {
                    $query->where('user_id_from', $contact_id)
                        ->where('user_id_to', $user_id);
                })
                ->orderBy('created_at', 'DESC')->limit(10)->get();
            $order[] = $c;
        }

        return response()->json([
            'status'  => true,
            'message' => 'List',
            'data'    => $order
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->only('user_id_to', 'text');
        $validator = Validator::make($data, [
            'user_id_to' => 'required|exists:users,id',
            'text'       => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $first = $errors->getMessages();
            return response()->json([
                'status'  => false,
                'message' => (reset($first))[0],
                'errors'  => $errors
            ]);
        }

        $user_id = $request->user()->id;

        $data['user_id_from'] = $user_id;
        // $user_to = User::find($data['user_id_to']);

        $current = date('Y-m-d H:i:s');
        $new_message = Message::create([
            'user_id_to'   => $data['user_id_to'],
            'user_id_from' => $user_id,
            'text'         => $data['text'],
            'created_at'   => $current
        ]);

        $data = Message::with(['user_from', 'user_to'])->find($new_message->id);
        $data->created_datetime = $current;

        broadcast(
                new EventsMessage($data['user_id_to'], [
                    "contact" => $request->user(),
                    "message" => $data
                ])
            );

        return response()->json([
            'status'  => true,
            'message' => 'Sent successfully!',
            'data'    => $data
        ]);
    }

    public function messages(Request $request, $contact_id)
    {
        $user_id = $request->user()->id;
        $data = Message::select('*',
                'created_at as created_datetime',
                DB::raw("(CASE user_id_from WHEN ".$user_id." THEN true ELSE false END) as sent_by_me"))
            ->where(function($query) use ($user_id, $contact_id) {
                $query->where('user_id_from', $user_id)
                    ->where('user_id_to', $contact_id);
            })
            ->orWhere(function($query) use ($user_id, $contact_id) {
                $query->where('user_id_from', $contact_id)
                    ->where('user_id_to', $user_id);
            })
            ->orderBy('created_at', 'DESC')->limit(20)->get();

        return response()->json([
            'status'  => true,
            'message' => 'List',
            'contact' => User::find($contact_id),
            'data'    => $data
        ]);
    }
}
