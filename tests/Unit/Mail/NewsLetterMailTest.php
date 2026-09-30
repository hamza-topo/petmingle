<?php

namespace Tests\Unit\Mail;

use App\Mail\NewsLetterMail;
use App\Models\NewsLetter;
use Tests\TestCase;

class NewsLetterMailTest extends TestCase
{
    public function test_newsletter_mail_uses_real_view_and_content(): void
    {
        $newsLetter = new NewsLetter([
            'title' => 'PetMingle Weekly Update',
            'content' => 'New pets and platform updates are available.',
            'type' => 1,
            'active' => true,
        ]);

        $mail = new NewsLetterMail($newsLetter);

        $mail->build();

        $this->assertSame(
            'PetMingle Weekly Update',
            $mail->subject
        );

        $html = $mail->render();

        $this->assertStringContainsString(
            'PetMingle Weekly Update',
            $html
        );

        $this->assertStringContainsString(
            'New pets and platform updates are available.',
            $html
        );
    }
}