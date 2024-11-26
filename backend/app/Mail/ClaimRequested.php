<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ClaimRequested extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Post $post, protected Request $request)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(auth()->user()->email, auth()->user()->profile->full_name),
            replyTo: [
                new Address($this->post->user->email, $this->post->user->profile->full_name),
            ],
            subject: 'QuestRetrieve - Someone wants to claim your found item',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.claim.requested',
            view: 'mail.claim.requested',
            with: [
                'item' => $this->post->subcategory->name,
                'type' => $this->post->type,
                'claimers_name' => auth()->user()->profile->full_name,
                'item_description' => $this->request->item_description,
                'where' => $this->request->where,
                'when' => $this->request->when,
                'message' => $this->request->message,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
