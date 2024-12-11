<?php

namespace App\Http\Controllers;

use App\Models\CustomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminCustomRequestController extends Controller
{
   public function index(): View
   {
      $customRequests = CustomRequest::with(['images', 'category.images'])
         ->orderBy('created_at', 'desc')
         ->get();

      return view('admin.custom_requests.index', compact('customRequests'));
   }

}