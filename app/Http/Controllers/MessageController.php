<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Shipment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Show chat page for a shipment
     */
    public function show($shipmentId)
    {
        // Get shipment with product and users
        $shipment = Shipment::with(['user', 'product.user'])
            ->where('id', $shipmentId)
            ->where(function($query) {
                // User must be either customer OR vendor of this shipment
                $query->where('user_id', Auth::id()) // Customer
                      ->orWhereHas('product', function($q) {
                          $q->where('user_id', Auth::id()); // Vendor
                      });
            })
            ->firstOrFail();

        // Get vendor from product relationship
        $vendor = $shipment->product->user ?? null;
        
        // Get customer
        $customer = $shipment->user;
        
        // Determine if current user is customer or vendor
        $isCustomer = Auth::id() === $shipment->user_id;
        $otherUser = $isCustomer ? $vendor : $customer;

        // Get messages
        $messages = Message::with(['sender', 'receiver'])
            ->where('shipment_id', $shipmentId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('shipment_id', $shipmentId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Inertia::render('Chat/Chat', [
            'shipment' => $shipment,
            'messages' => $messages,
            'currentUser' => Auth::user(),
            'otherUser' => $otherUser,
            'userType' => $isCustomer ? 'customer' : 'vendor',
        ]);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request, $shipmentId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $shipment = Shipment::with('product')->findOrFail($shipmentId);
        
        // Get customer and vendor IDs
        $customerId = $shipment->user_id;
        $vendorId = $shipment->product->user_id ?? null;
        
        // Current user must be either customer or vendor
        $currentUserId = Auth::id();
        
        if (!in_array($currentUserId, [$customerId, $vendorId])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Determine receiver
        $receiverId = $currentUserId === $customerId ? $vendorId : $customerId;

        // If receiver is null (vendor doesn't exist), send error
        if (!$receiverId) {
            return response()->json(['error' => 'Vendor not found for this shipment'], 404);
        }

        $message = Message::create([
            'shipment_id' => $shipmentId,
            'sender_id' => $currentUserId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ]);
    }
    
    /**
     * Get all shipments where current user has conversations
     */
    public function conversations()
    {
        $user = Auth::user();
        
        // Get shipments where user is either customer or vendor
        $shipments = Shipment::with(['user', 'product.user', 'messages' => function($q) use ($user) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }])
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id) // User is customer
                      ->orWhereHas('product', function($q) use ($user) {
                          $q->where('user_id', $user->id); // User is vendor
                      });
            })
            ->whereHas('messages') // Only shipments with messages
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($shipment) use ($user) {
                // Get last message
                $lastMessage = $shipment->messages->first();
                
                // Get unread count
                $unreadCount = Message::where('shipment_id', $shipment->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                    
                // Get other user
                $otherUser = $shipment->user_id === $user->id 
                    ? ($shipment->product->user ?? null)  // If user is customer, other is vendor
                    : $shipment->user;          // If user is vendor, other is customer
                
                return [
                    'id' => $shipment->id,
                    'order_number' => $shipment->order_number,
                    'product_name' => $shipment->product_name,
                    'last_message' => $lastMessage ? $lastMessage->message : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                    'unread_count' => $unreadCount,
                    'other_user' => $otherUser ? [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                        'is_verified' => $otherUser->is_verified,
                    ] : null,
                    'created_at' => $shipment->created_at,
                ];
            });

        return Inertia::render('Chat/Conversations', [
            'conversations' => $shipments,
            'user' => $user,
        ]);
    }
    
    /**
     * API endpoint for polling messages
     */
    public function getMessages($shipmentId)
    {
        $messages = Message::with(['sender', 'receiver'])
            ->where('shipment_id', $shipmentId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json(['messages' => $messages]);
    }

    /**
     * Get unread message count for current user
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}