<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SharePackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leadName;
    public $packageName;
    public $documents;

    /**
     * Create a new message instance.
     */
    public function __construct($leadName, $packageName, $documents = [])
    {
        $this->leadName = $leadName;
        $this->packageName = $packageName;
        $this->documents = $documents; // array of URLs
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject("Package Details: {$this->packageName}")
                     ->view('emails.share-package')
                     ->with([
                         'leadName' => $this->leadName,
                         'packageName' => $this->packageName,
                         'documents' => $this->documents,
                     ]);

        // Attach all documents
        foreach ($this->documents as $doc) {
            $path = public_path(parse_url($doc, PHP_URL_PATH));
            if (file_exists($path)) {
                $mail->attach($path);
            }
        }

        return $mail;
    }
}
