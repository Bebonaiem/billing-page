<?php

namespace App\Constants;

class BillingConstants
{
    // Invoice statuses
    const INVOICE_STATUS_DRAFT = 'draft';
    const INVOICE_STATUS_UNPAID = 'unpaid';
    const INVOICE_STATUS_PAID = 'paid';
    const INVOICE_STATUS_CANCELLED = 'cancelled';
    const INVOICE_STATUS_REFUNDED = 'refunded';
    const INVOICE_STATUS_COLLECTIONS = 'collections';

    // Order statuses
    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_PROCESSING = 'processing';
    const ORDER_STATUS_COMPLETED = 'completed';
    const ORDER_STATUS_CANCELLED = 'cancelled';
    const ORDER_STATUS_REFUNDED = 'refunded';

    // Service statuses
    const SERVICE_STATUS_ACTIVE = 'active';
    const SERVICE_STATUS_SUSPENDED = 'suspended';
    const SERVICE_STATUS_TERMINATED = 'terminated';
    const SERVICE_STATUS_PENDING = 'pending';
    const SERVICE_STATUS_CANCELLED = 'cancelled';

    // Payment statuses
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_COMPLETED = 'completed';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_CANCELLED = 'cancelled';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    // Ticket statuses
    const TICKET_STATUS_OPEN = 'open';
    const TICKET_STATUS_ANSWERED = 'answered';
    const TICKET_STATUS_CUSTOMER_REPLY = 'customer_reply';
    const TICKET_STATUS_CLOSED = 'closed';
    const TICKET_STATUS_ON_HOLD = 'on_hold';

    // Ticket priorities
    const TICKET_PRIORITY_LOW = 'low';
    const TICKET_PRIORITY_NORMAL = 'normal';
    const TICKET_PRIORITY_HIGH = 'high';
    const TICKET_PRIORITY_URGENT = 'urgent';

    // User statuses
    const USER_STATUS_ACTIVE = 'active';
    const USER_STATUS_SUSPENDED = 'suspended';
    const USER_STATUS_BANNED = 'banned';
    const USER_STATUS_INACTIVE = 'inactive';
    const USER_STATUS_STAFF = 'staff';
    const USER_STATUS_SUPPORT = 'support';

    // Product types
    const PRODUCT_TYPE_GAME_SERVER = 'game_server';
    const PRODUCT_TYPE_WEB_HOSTING = 'web_hosting';
    const PRODUCT_TYPE_VPS = 'vps';
    const PRODUCT_TYPE_DEDICATED = 'dedicated';
    const PRODUCT_TYPE_DOMAIN = 'domain';
    const PRODUCT_TYPE_CUSTOM = 'custom';

    // Billing cycles
    const BILLING_CYCLE_HOURLY = 'hourly';
    const BILLING_CYCLE_DAILY = 'daily';
    const BILLING_CYCLE_WEEKLY = 'weekly';
    const BILLING_CYCLE_MONTHLY = 'monthly';
    const BILLING_CYCLE_QUARTERLY = 'quarterly';
    const BILLING_CYCLE_SEMI_ANNUALLY = 'semi_annually';
    const BILLING_CYCLE_ANNUALLY = 'annually';
    const BILLING_CYCLE_BIENNIALLY = 'biennially';
    const BILLING_CYCLE_ONE_TIME = 'one_time';

    // Payment methods
    const PAYMENT_METHOD_STRIPE = 'stripe';
    const PAYMENT_METHOD_PAYPAL = 'paypal';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_METHOD_CREDIT = 'credit';
    const PAYMENT_METHOD_BALANCE = 'balance';

    // Credit transaction types
    const CREDIT_TYPE_ADDITION = 'addition';
    const CREDIT_TYPE_DEDUCTION = 'deduction';
    const CREDIT_TYPE_REFUND = 'refund';
    const CREDIT_TYPE_PAYMENT = 'payment';

    // Coupon types
    const COUPON_TYPE_PERCENTAGE = 'percentage';
    const COUPON_TYPE_FIXED = 'fixed';

    // Default values
    const DEFAULT_CURRENCY = 'USD';
    const DEFAULT_LANGUAGE = 'en';
    const DEFAULT_TIMEZONE = 'UTC';
    const DEFAULT_LATE_FEE_PERCENTAGE = 5;
    const DEFAULT_TRIAL_DAYS = 0;

    // Time-based constants
    const PASSWORD_RESET_TOKEN_HOURS = 24;
    const LOGIN_THROTTLE_ATTEMPTS = 5;
    const LOGIN_THROTTLE_MINUTES = 1;
    const REGISTRATION_THROTTLE_ATTEMPTS = 3;
    const REGISTRATION_THROTTLE_MINUTES = 60;

    // Financial precision
    const DECIMAL_PRECISION = 15;
    const DECIMAL_SCALE = 4;
}