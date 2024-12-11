<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminCustomRequestController extends Controller
{
   public function index(): View
   {
      $customRequests = CustomRequest::with(['user', 'images', 'category.images'])
         ->orderBy('created_at', 'desc')
         ->get();

      return view('admin.custom_requests.index', compact('customRequests'));
   }

   public function show(CustomRequest $customRequest): View
   {
      $customRequest->load( ['user', 'images', 'category.images']);
      return view('admin.custom_requests.show', compact('customRequest'));
   }

   public function destroy (CustomRequest $customRequest): RedirectResponse
   {
      $customRequest->delete();

      return redirect()->route('admin.custom_requests.index');
   }
}