<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return view('portal.chat.index');
        } catch (\Exception $e) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $e->getFile() . ', Line: ' . $e->getLine() . ', Message: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve conversations: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function loadChats()
    {
        try {
            $conversations = Conversation::with([
                'participants',
                'messages' => function ($query) {
                    $query->orderByDesc('created_at')->limit(1); // Fetch only the latest message per conversation
                }
            ])
                ->whereHas('participants', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->orderByDesc(
                    Message::select('created_at')
                        ->whereColumn('conversation_id', 'conversations.id')
                        ->latest()
                        ->limit(1)
                ) // Order by latest message timestamp
                ->get()
                ->map(function ($conversation) {
                    // Get last message
                    $lastMessage = $conversation->messages->first();

                    // Count unread messages
                    $unreadCount = Message::where('conversation_id', $conversation->id)
                        ->where('receiver_id', Auth::id()) // Assuming `receiver_id` exists
                        ->whereNot('status', 'read')
                        ->count();

                    return [
                        'conversation_id' => $conversation->id,
                        'id' => $conversation->participants->where('user_id', '!=', Auth::id())->first()->user->id ?? 'No Id',
                        'name' => $conversation->participants->where('user_id', '!=', Auth::id())->first()->user->name ?? 'No Name',
                        'last_message' => $lastMessage ? $lastMessage->message : null,
                        'last_message_time' => $lastMessage ? $lastMessage->created_at->format('H:i') : null,
                        'unread_count' => $unreadCount
                    ];
                });

            return response()->json([
                'success' => true,
                'chats' => $conversations
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $e->getFile() . ', Line: ' . $e->getLine() . ', Message: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve conversations: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getUser(string $id)
    {
        try {
            // Retrieve the user
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            // Retrieve the most recent conversation between authenticated user and selected user
            $conversation = Conversation::whereHas('participants', function ($query) {
                $query->where('user_id', Auth::id());
            })
                ->whereHas('participants', function ($query) use ($id) {
                    $query->where('user_id', $id);
                })
                ->latest('id') // or latest('created_at') if needed
                ->first();

            return response()->json([
                'success' => true,
                'user' => $user,
                'conversation_id' => $conversation->id ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $e->getFile() . ', Line: ' . $e->getLine() . ', Message: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to retrieve user or conversation. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMessages(string $conversationId)
    {
        try {
            // Retrieve messages with sender details
            $messages = Message::where('conversation_id', $conversationId)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $e->getFile() . ', Line: ' . $e->getLine() . ', Message: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve messages. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000',
            ]);

            // Get sender and receiver IDs
            $senderId = Auth::user()->id;
            $receiverId = $request->receiver_id;
            $messageText = $request->message;

            // Check if a private conversation exists between sender and receiver
            $conversation = Conversation::where('type', 'private')
                ->whereHas('participants', function ($query) use ($senderId) {
                    $query->where('user_id', $senderId);
                })
                ->whereHas('participants', function ($query) use ($receiverId) {
                    $query->where('user_id', $receiverId);
                })
                ->first();

            // If no conversation exists, create a new one
            if (!$conversation) {
                $conversation = Conversation::create([
                    'name' => null, // Can be set for group chats
                    'type' => 'private',
                ]);

                // Add sender and receiver as participants
                ConversationParticipant::insert([
                    ['conversation_id' => $conversation->id, 'user_id' => $senderId, 'created_at' => now(), 'updated_at' => now()],
                    ['conversation_id' => $conversation->id, 'user_id' => $receiverId, 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            // Store the message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => $messageText,
                'status' => 'sent',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $e->getFile() . ', Line: ' . $e->getLine() . ', Message: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update employee.' . $e->getMessage(),
            ], 500);
        }
    }
}
