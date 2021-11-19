<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class UserVerify extends Mailable
{
    use Queueable, SerializesModels;
    
    public $data;
 
    public function __construct($data) {
        $this->data = $data;
    }
 
    public function build() {
        return $this->from('admin@elsimil.com', 'ELSIMIL')
            ->subject('Verifikasi Email Aktivasi User')
            ->markdown('mails.verify');
    }
}