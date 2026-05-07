{{-- Footer Component --}}
<footer class="glass-effect border-t border-custom">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 accent-bg rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold accent-text">{{ config('app.name', 'BillingHub') }}</h3>
                </div>
                <p class="text-secondary mb-4 max-w-md">
                    Professional billing and invoicing solution for service providers. Manage clients, invoices, payments, and automate your business with ease.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069 3.204 0 3.584.012 4.849.069 3.26.149 4.771 1.699 4.919 4.92.058 1.265.07 1.645.07 4.849 0 3.204-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.645.07-4.849.07zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.374 0 0 5.373 0 12s5.374 12 12 12 12-5.373 12-12S18.626 0 12 0zm5.568 13.371C17.63 14.986 16.247 16 14.5 16c-1.988 0-3.516-1.357-3.516-3.125S12.512 9.75 14.5 9.75c1.747 0 3.13 1.014 3.568 2.629.19.748.19 1.574 0 2.321C17.63 16.314 16.247 17.25 14.5 17.25c-1.988 0-3.516-1.357-3.516-3.125s1.528-3.125 3.516-3.125c1.747 0 3.13 1.014 3.568 2.629.19.748.19 1.574 0 2.321zm-11.136 0C6.494 14.986 5.111 16 3.364 16c-1.988 0-3.516-1.357-3.516-3.125S1.376 9.75 3.364 9.75c1.747 0 3.13 1.014 3.568 2.629.19.748.19 1.574 0 2.321C6.494 16.314 5.111 17.25 3.364 17.25c-1.988 0-3.516-1.357-3.516-3.125S1.376 9.75 3.364 9.75c1.747 0 3.13 1.014 3.568 2.629z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-secondary hover:text-white transition-colors duration-200">Home</a></li>
                    <li><a href="{{ route('order') }}" class="text-secondary hover:text-white transition-colors duration-200">Services</a></li>
                    <li><a href="{{ route('about') }}" class="text-secondary hover:text-white transition-colors duration-200">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-secondary hover:text-white transition-colors duration-200">Contact</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Support</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('help') }}" class="text-secondary hover:text-white transition-colors duration-200">Help Center</a></li>
                    <li><a href="{{ route('docs') }}" class="text-secondary hover:text-white transition-colors duration-200">Documentation</a></li>
                    <li><a href="{{ route('api') }}" class="text-secondary hover:text-white transition-colors duration-200">API</a></li>
                    <li><a href="{{ route('status') }}" class="text-secondary hover:text-white transition-colors duration-200">System Status</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Legal</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('privacy') }}" class="text-secondary hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="text-secondary hover:text-white transition-colors duration-200">Terms of Service</a></li>
                    <li><a href="{{ route('cookies') }}" class="text-secondary hover:text-white transition-colors duration-200">Cookie Policy</a></li>
                    <li><a href="{{ route('gdpr') }}" class="text-secondary hover:text-white transition-colors duration-200">GDPR</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-custom mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-tertiary text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} {{ config('app.name', 'BillingHub') }}. All rights reserved.
                </p>
                <div class="flex items-center space-x-6">
                    <span class="text-tertiary text-sm">Made with ❤️ using Laravel & TailwindCSS</span>
                    <div class="flex space-x-4">
                        <a href="#" class="text-tertiary hover:text-white text-sm transition-colors duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-tertiary hover:text-white text-sm transition-colors duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
