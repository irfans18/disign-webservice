<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Request as ModelsRequest;


class ShowAllRequests extends Component
{
   public function render()
   {
      $requests = $this->getAllRequests();

      return view('livewire.show-all-requests', ['requests' => $requests]);
   }
   public function getAllRequests()
   {
      // $req = ModelsRequest::all();
      $req = ModelsRequest::with('user')->get();
      // $req =User::with(['requests' => function ($query) {
      //    $query->select('certificate_id', 'status');
      // }])->select('username', 'email')->find($userId);
      // dd($req);
      return $req;
   }
   public function getUserDetail($id)
   {
      $user = User::find($id);
      return $user->name;
   }
}
