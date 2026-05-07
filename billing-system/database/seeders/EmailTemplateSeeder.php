<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'welcome',
                'subject' => 'Welcome to {company_name}',
                'body_html' => '<p>Hello {first_name},</p><p>Welcome to {company_name}! Your account has been successfully created.</p><p>Best regards,<br>{company_name} Team</p>',
                'body_text' => "Hello {first_name},\n\nWelcome to {company_name}! Your account has been successfully created.\n\nBest regards,\n{company_name} Team",
            ],
            [
                'name' => 'password_reset',
                'subject' => 'Password Reset Request',
                'body_html' => '<p>Hello {first_name},</p><p>You requested a password reset. Click the link below to reset your password:</p><p><a href="{reset_url}">Reset Password</a></p><p>If you did not request this, please ignore this email.</p>',
                'body_text' => "Hello {first_name},\n\nYou requested a password reset. Click the link below to reset your password:\n\n{reset_url}\n\nIf you did not request this, please ignore this email.",
            ],
            [
                'name' => 'invoice_created',
                'subject' => 'New Invoice #{invoice_number} - {company_name}',
                'body_html' => '<p>Hello {first_name},</p><p>A new invoice has been generated for your account.</p><p><strong>Invoice Number:</strong> {invoice_number}<br><strong>Amount:</strong> {amount}<br><strong>Due Date:</strong> {due_date}</p><p>You can view and pay your invoice at: <a href="{invoice_url}">{invoice_url}</a></p>',
                'body_text' => "Hello {first_name},\n\nA new invoice has been generated for your account.\n\nInvoice Number: {invoice_number}\nAmount: {amount}\nDue Date: {due_date}\n\nYou can view and pay your invoice at: {invoice_url}",
            ],
            [
                'name' => 'order_confirmation',
                'subject' => 'Order Confirmation #{order_number} - {company_name}',
                'body_html' => '<p>Hello {first_name},</p><p>Your order <strong>#{order_number}</strong> has been received.</p><p><strong>Order Date:</strong> {order_date}<br><strong>Total:</strong> {total}</p><p>You can review your account at: <a href="{order_url}">{order_url}</a></p>',
                'body_text' => "Hello {first_name},\n\nYour order #{order_number} has been received.\n\nOrder Date: {order_date}\nTotal: {total}\n\nYou can review your account at: {order_url}",
            ],
            [
                'name' => 'invoice_paid',
                'subject' => 'Payment Received - Invoice #{invoice_number}',
                'body_html' => '<p>Hello {first_name},</p><p>Thank you for your payment. We have received payment for invoice #{invoice_number}.</p><p><strong>Amount Paid:</strong> {amount}</p><p>Thank you for your business!</p>',
                'body_text' => "Hello {first_name},\n\nThank you for your payment. We have received payment for invoice #{invoice_number}.\n\nAmount Paid: {amount}\n\nThank you for your business!",
            ],
            [
                'name' => 'service_activated',
                'subject' => 'Service Activated - {service_name}',
                'body_html' => '<p>Hello {first_name},</p><p>Your service <strong>{service_name}</strong> has been activated.</p><p>You can manage your service at: <a href="{service_url}">{service_url}</a></p>',
                'body_text' => "Hello {first_name},\n\nYour service {service_name} has been activated.\n\nYou can manage your service at: {service_url}",
            ],
            [
                'name' => 'service_suspended',
                'subject' => 'Service Suspended - {service_name}',
                'body_html' => '<p>Hello {first_name},</p><p>Your service <strong>{service_name}</strong> has been suspended due to unpaid invoice(s).</p><p>Please log in to your account to make payment and restore your service.</p>',
                'body_text' => "Hello {first_name},\n\nYour service {service_name} has been suspended due to unpaid invoice(s).\n\nPlease log in to your account to make payment and restore your service.",
            ],
            [
                'name' => 'suspension_notice',
                'subject' => 'Suspension Notice - {service_name}',
                'body_html' => '<p>Hello {first_name},</p><p>Your service <strong>{service_name}</strong> is at risk of suspension.</p><p><strong>Suspension Date:</strong> {suspension_date}</p><p>Reason: {reason}</p><p>Visit your client area to resolve this issue: <a href="{support_url}">{support_url}</a></p>',
                'body_text' => "Hello {first_name},\n\nYour service {service_name} is at risk of suspension.\n\nSuspension Date: {suspension_date}\nReason: {reason}\n\nVisit your client area to resolve this issue: {support_url}",
            ],
            [
                'name' => 'ticket_reply',
                'subject' => 'New Reply to Ticket #{ticket_number}',
                'body_html' => '<p>Hello {first_name},</p><p>A new reply has been posted to your support ticket <strong>{ticket_subject}</strong>.</p><p>You can view the ticket at: <a href="{ticket_url}">{ticket_url}</a></p>',
                'body_text' => "Hello {first_name},\n\nA new reply has been posted to your support ticket {ticket_subject}.\n\nYou can view the ticket at: {ticket_url}",
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
