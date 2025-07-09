<?php

namespace App\Mail;

use App\Models\Reimbursement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewReimbursementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reimbursement;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Reimbursement  $reimbursement
     * @return void
     */
    public function __construct(Reimbursement $reimbursement)
    {
        $this->reimbursement = $reimbursement;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pengajuan Reimbursement Baru')
            ->view('emails.reimbursements.new')
            ->with([
                'reimbursement' => $this->reimbursement,
            ]);
    }
}
