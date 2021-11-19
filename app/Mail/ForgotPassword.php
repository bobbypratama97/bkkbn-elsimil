<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    
    public $data;
 
    public function __construct($data) {
        $this->data = $data;
    }
 
    public function build() {
        return $this->from('admin@elsimil.com', 'ELSIMIL')
            ->subject('Atur Ulang Password Akun')
            ->markdown('mails.forgot');
    }
}