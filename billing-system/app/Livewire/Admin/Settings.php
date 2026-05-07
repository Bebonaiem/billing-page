<?php

namespace App\Livewire\Admin;

use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\PaymentGateway;
use Livewire\Component;

class Settings extends Component
{
    public string $activeTab = 'general';
    
    // General settings
    public array $general = [
        'company_name' => '',
        'company_email' => '',
        'support_email' => '',
        'default_currency' => 'USD',
        'default_language' => 'en',
        'timezone' => 'UTC',
    ];
    
    // Billing settings
    public array $billing = [
        'invoice_prefix' => 'INV',
        'invoice_due_days' => 14,
        'late_fee_enabled' => false,
        'late_fee_amount' => 5.00,
        'late_fee_after_days' => 7,
        'auto_suspend_enabled' => true,
        'suspend_after_days' => 7,
        'auto_terminate_enabled' => false,
        'terminate_after_days' => 30,
    ];

    public array $emailTemplates = [];
    public ?int $editingEmailTemplateId = null;

    public function mount()
    {
        $this->loadSettings();
        $this->loadEmailTemplates();
    }

    public function loadSettings()
    {
        // Load general settings
        $this->general['company_name'] = Setting::get('company_name', config('app.name'), 'general');
        $this->general['company_email'] = Setting::get('company_email', '', 'general');
        $this->general['support_email'] = Setting::get('support_email', '', 'general');
        $this->general['default_currency'] = Setting::get('default_currency', 'USD', 'general');
        $this->general['default_language'] = Setting::get('default_language', 'en', 'general');
        $this->general['timezone'] = Setting::get('timezone', 'UTC', 'general');
        
        // Load billing settings
        $this->billing['invoice_prefix'] = Setting::get('invoice_prefix', 'INV', 'billing');
        $this->billing['invoice_due_days'] = Setting::get('invoice_due_days', 14, 'billing');
        $this->billing['late_fee_enabled'] = Setting::get('late_fee_enabled', false, 'billing');
        $this->billing['late_fee_amount'] = Setting::get('late_fee_amount', 5.00, 'billing');
        $this->billing['late_fee_after_days'] = Setting::get('late_fee_after_days', 7, 'billing');
        $this->billing['auto_suspend_enabled'] = Setting::get('auto_suspend_enabled', true, 'billing');
        $this->billing['suspend_after_days'] = Setting::get('suspend_after_days', 7, 'billing');
        $this->billing['auto_terminate_enabled'] = Setting::get('auto_terminate_enabled', false, 'billing');
        $this->billing['terminate_after_days'] = Setting::get('terminate_after_days', 30, 'billing');
    }

    public function saveGeneralSettings()
    {
        foreach ($this->general as $key => $value) {
            Setting::set($key, $value, 'general');
        }
        
        session()->flash('success', 'General settings saved successfully.');
    }

    public function saveBillingSettings()
    {
        foreach ($this->billing as $key => $value) {
            Setting::set($key, $value, 'billing');
        }
        
        session()->flash('success', 'Billing settings saved successfully.');
    }

    public function loadEmailTemplates(): void
    {
        $this->emailTemplates = EmailTemplate::query()
            ->orderBy('name')
            ->get(['id', 'name', 'subject', 'body_html', 'body_text', 'from_name', 'from_email', 'is_active'])
            ->keyBy('id')
            ->map(fn (EmailTemplate $template) => [
                'id' => $template->id,
                'name' => $template->name,
                'subject' => $template->subject,
                'body_html' => $template->body_html,
                'body_text' => $template->body_text,
                'from_name' => $template->from_name,
                'from_email' => $template->from_email,
                'is_active' => $template->is_active,
            ])
            ->toArray();
    }

    public function editEmailTemplate(int $templateId): void
    {
        $this->editingEmailTemplateId = $templateId;
    }

    public function saveEmailTemplate(): void
    {
        if (!$this->editingEmailTemplateId) {
            return;
        }

        $template = EmailTemplate::findOrFail($this->editingEmailTemplateId);
        $template->update([
            'subject' => data_get($this->emailTemplates, $this->editingEmailTemplateId . '.subject', $template->subject),
            'body_html' => data_get($this->emailTemplates, $this->editingEmailTemplateId . '.body_html', $template->body_html),
            'body_text' => data_get($this->emailTemplates, $this->editingEmailTemplateId . '.body_text', $template->body_text),
            'from_name' => data_get($this->emailTemplates, $this->editingEmailTemplateId . '.from_name', $template->from_name),
            'from_email' => data_get($this->emailTemplates, $this->editingEmailTemplateId . '.from_email', $template->from_email),
            'is_active' => (bool) data_get($this->emailTemplates, $this->editingEmailTemplateId . '.is_active', $template->is_active),
        ]);

        $this->loadEmailTemplates();
        $this->editingEmailTemplateId = null;

        session()->flash('success', 'Email template saved successfully.');
    }

    public function cancelEmailTemplateEdit(): void
    {
        $this->editingEmailTemplateId = null;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $currencies = Currency::active()->get();
        $gateways = PaymentGateway::all();
        
        return view('livewire.admin.settings', [
            'currencies' => $currencies,
            'gateways' => $gateways,
        ]);
    }
}
