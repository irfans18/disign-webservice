<?php

namespace App\Http\Livewire;

use App\Models\Certificate;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\Request as ModelsRequest;

class RequestDetail extends Component
{
   public $req;


   public function render($id)
   {
      $this->req = ModelsRequest::find($id);
      $user = ModelsRequest::find($id)->user;
      // dd($req);
      $pdf = $this->showPdf($this->req->filepath);
      return view('livewire.request-detail', ['detail' => $this->req, 'user' => $user, 'pdf' => $pdf]);
   }
   public function getDetail($id)
   {
      $this->req = ModelsRequest::find($id);
      $user = ModelsRequest::find($id)->user;
      // dd($req);
      $pdf = $this->showPdf($this->req->filepath);
      // return view('livewire.request-detail', ['detail' => $this->req, 'user' => $user, 'pdf' => $pdf]);
   }
   public function showPdf($filename)
   {
      // $path = 'pdfs/' . $filename;
      $path = 'pdfs/' . $filename;
      $pdfPath = Storage::disk('public')->url($path);
      return $pdfPath;
   }
   public function onAccept($id)
   {
      $this->getDetail($id);
      $req = $this->req;
      // $req->status = ModelsRequest::APPROVED;
      $req->status = ModelsRequest::APPROVED;
      $req->update();

      $cert = Certificate::find($req->certificate_id);
      $cert->is_revoked = true;
      $cert->revocation_detail = $req->revocation_detail;
      $cert->revoked_at = $req->revoked_at;
      $cert->revoked_timestamp = $req->revoked_timestamp;
      $cert->update();
      return redirect()->route('dashboard');
   }
   public function onDecline($id)
   {
      $this->getDetail($id);
      $req = $this->req;
      $req->status = ModelsRequest::REJECTED;
      $req->update();
      
      $cert = Certificate::find($req->certificate_id);
      $cert->is_revoked = false;
      $cert->update();

      return redirect()->route('dashboard');
   }
}
