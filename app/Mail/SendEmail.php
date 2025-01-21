<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['title'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.notification', // Your view
            with: ['data' => $this->data], // Pass the data to the view
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // foreach ($this->data['filePaths'] as $filePath) {
        //     $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath);
        //     // dd($attachments);
        // }
        foreach ($this->data['filePaths'] as $filePath) {

            // $fullPath = public_path($filePath); // Resolve path dynamically
            // if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath);
            // } else {
            //     $attachments[] = "Attachment file does not exist: {$filePath}";
            // }
        }
        // dd($attachments);
        return $attachments;
    }
}
