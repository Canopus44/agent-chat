<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversations = Conversation::all();

        if (!$conversations) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversations not found'
            ], 404);
        }

        return response()->json($conversations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // No se necesita implementar este método en este caso
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $conversation = Conversation::create($request->all());
        return response()->json($conversation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function showByAgent(Request $request, $agent_id)
    {

        if (!$request->status || ($request->status != 'open' && $request->status != 'closed')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a valid status | open or closed'
            ], 404);
        }

        if ($request->status == 'open') {
            $conversation = Conversation::where('agent', $agent_id)->where('status', 'open')->get();
        } else {
            $conversation = Conversation::where('agent', $agent_id)->where('status', 'closed')->get();
        }


        if (!$agent_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a valid agent id'
            ], 404);
        }

        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ], 404);
        }

        // Buscamos el ultimo mensaje que coresponde a esta conversacion y lo agregamos al objeto usando el session_chat

        $session_chat = $conversation->pluck('session_chat')->all();

        $conversation = json_decode($conversation, true);

        foreach ($session_chat as $key => $session) {
            $last_message = $this->getLastMessage($session);
            $conversation[$key]['lastest_message'] = $last_message;
        }

        return response()->json(array_reverse($conversation));
    }

    /**
     * Obtiene el ultimo mensaje para la session chat que nos llega por parametro
     */
    private function getLastMessage($session_chat)
    {

        $message = Message::where('session_chat', $session_chat)
            ->latest()
            ->first();

        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'No messages found'
            ], 404);
        }

        return $message->messaging['message']['text'];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function edit(Conversation $conversation)
    {
        // No se necesita implementar este método en este caso
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conversation $conversation)
    {
        $conversation->update($request->all());
        return response()->json($conversation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return response()->json(['message' => 'Conversation successfully deleted']);
    }

    public function showMessagesByConversation($session_chat)
    {

        $conversation = Conversation::where('session_chat', $session_chat)->get();
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ], 404);
        }

        $messages = Message::where('session_chat', $session_chat)->get();
        if (!$messages) {
            return response()->json([
                'status' => 'error',
                'message' => 'Messages not found'
            ], 404);
        }

        return response()->json($messages);
    }
}
