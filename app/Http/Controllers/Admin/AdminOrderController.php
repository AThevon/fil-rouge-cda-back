<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Enum;

class AdminOrderController extends Controller
{
   public function index(): View
   {
      $orders = Order::with(['orderProducts.product.images', 'user'])
         ->orderBy('created_at', 'desc')
         ->get();

      return view('admin.orders.index', compact('orders'));
   }

   public function show(Order $order): View
   {
      $order->load(['orderProducts.product.images', 'user']);
      return view('admin.orders.show', compact('order'));
   }

   public function updateStatus(Request $request, Order $order)
   {
      $validator = Validator::make($request->all(), [
         'status' => ['required', new Enum(OrderStatus::class)],
      ]);

      if ($validator->fails()) {
         return redirect()->back()
            ->withErrors($validator)
            ->withInput();
      }

      $order->update(['status' => $request->status]);

      return redirect()->route('admin.orders.index')
         ->with('success', 'Order status updated successfully!');
   }

   public function destroy(Order $order)
   {
      if ($order->status !== OrderStatus::PENDING) {
         return redirect()->route('admin.orders.index')
            ->with('error', 'Cannot delete an order that is not in pending status.');
      }

      $order->orderProducts()->delete();
      $order->delete();

      return redirect()->route('admin.orders.index')
         ->with('success', 'Order deleted successfully!');
   }
}