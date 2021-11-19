<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class Approval extends Mailable
{
    use Queueable, SerializesModels;
    
    public $data;
 
    public function __construct($data) {
        $this->data = $data;
    }
 
    public function build() {
        return $this->from('admin@elsimil.com', 'ELSIMIL')
            ->subject('Pengajuan Approval Kuesioner')
            ->markdown('mails.approval');
    }
}