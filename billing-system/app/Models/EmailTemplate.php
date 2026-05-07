<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body_html',
        'body_text',
        'from_name',
        'from_email',
        'attachments',
        'is_active',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function parseSubject(array $variables): string
    {
        $subject = $this->subject;

        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, $value, $subject);
        }

        return $subject;
    }

    public function parseContent(array $variables): string
    {
        $bodyHtml = $this->body_html;

        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $bodyHtml = str_replace($placeholder, $value, $bodyHtml);
        }

        return $bodyHtml;
    }

    public function parseTextContent(array $variables): string
    {
        $bodyText = $this->body_text ?? '';

        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $bodyText = str_replace($placeholder, $value, $bodyText);
        }

        return $bodyText;
    }
}
