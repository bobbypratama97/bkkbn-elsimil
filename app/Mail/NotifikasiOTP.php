<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class NotifikasiOTP extends Mailable
{
    use Queueable, SerializesModels;
    
    public $data;
 
    public function __construct($data) {
        $this->data = $data;
    }
 
    public function build() {
        return $this->from('sender@example.com', 'ELSIMIL')
            ->subject('Atur Ulang Kata Sandi Akun')
            ->view('mails.demo');
    }
}