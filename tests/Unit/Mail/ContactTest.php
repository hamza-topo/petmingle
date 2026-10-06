<?php

namespace Tests\Unit\Mail;

use App\Mail\Contact;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_contact_mail_contains_the_submission_and_escapes_html(): void
    {
        $mail = new Contact([
            'name' => 'Alice',
            'email' => 'alice@example.test',
            'subject' => 'Need help',
            'message' => "First line\n<script>alert('x')</script>",
        ]);
        $mail->build();

        $this->assertStringContainsString('Need help', $mail->subject);
        $this->assertSame('alice@example.test', $mail->replyTo[0]['address']);
        $html = $mail->render();
        $this->assertStringContainsString('Alice', $html);
        $this->assertStringContainsString('alice@example.test', $html);
        $this->assertStringContainsString('First line<br', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('departure', $html);
    }
}
