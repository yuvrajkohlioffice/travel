<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharePackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leadName;
    public $subjectText;
    public $bodyText;
    public $documentUrls;
    private array $localAttachments;

    public function __construct(
        $leadName,
        $subjectText,
        $bodyText,
        array $attachments = [],
        array $documentUrls = []
    ) {
        $this->leadName        = $leadName;
        $this->subjectText     = $subjectText;
        $this->bodyText        = $bodyText;
        $this->documentUrls    = $documentUrls;
        $this->localAttachments = $attachments;
    }

    public function build()
    {
        $mail = $this->subject($this->subjectText)
                     ->view('emails.share-package')
                     ->with([
                         'leadName' => $this->leadName,
                         'bodyText' => $this->bodyText,
                         'documentUrls' => $this->documentUrls,
                     ]);

        foreach ($this->localAttachments as $file) {
            if (is_string($file) && file_exists($file)) {
                $mail->attach($file);
            }
        }

        return $mail;
    }
}
