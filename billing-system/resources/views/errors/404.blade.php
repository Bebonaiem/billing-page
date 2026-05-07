@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center particle-bg">
    <!-- Particle Effects -->
    <div class="particle-container">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <div class="max-w-2xl mx-auto p-8 text-center">
        <!-- Animated 404 Illustration -->
        <div class="animate-bounce-in mb-8">
            <div class="relative inline-block">
                <!-- Glowing 404 Text -->
                <div class="text-9xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 animate-pulse-glow mb-4">
                    404
                </div>
                
                <!-- Animated Character -->
                <div class="relative w-64 h-64 mx-auto animate-float">
                    <div class="glass-effect-3d rounded-3xl p-8 border border-custom shadow-glow">
                        <div class="text-center">
                            <!-- Robot Face -->
                            <div class="relative w-24 h-24 mx-auto mb-4">
                                <div class="w-16 h-16 accent-bg rounded-full mx-auto animate-morph"></div>
                                <div class="absolute top-0 left-1/2 w-4 h-4 accent-bg rounded-full animate-pulse"></div>
                                <div class="absolute top-0 right-1/2 w-4 h-4 accent-bg rounded-full animate-pulse"></div>
                                <div class="absolute top-4 left-1/4 w-3 h-3 accent-bg rounded-full animate-pulse"></div>
                                <div class="absolute top-4 right-1/4 w-3 h-3 accent-bg rounded-full animate-pulse"></div>
                            </div>
                            
                            <!-- Message -->
                            <h2 class="text-2xl font-bold text-white mb-4 animate-glow-scan">
                                Oops! Page Not Found
                            </h2>
                            <p class="text-secondary">
                                The page you're looking for seems to have vanished into the digital void.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search Suggestions -->
        <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.3s">
            <h3 class="text-xl font-bold text-white mb-4">Maybe you're looking for?</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('home') }}" class="block p-4 bg-card/30 rounded-xl hover-lift group cursor-pointer transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 accent-bg rounded-lg flex items-center justify-center mr-3 group-hover:animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7 7 7 7M5 10v10a1 1 0 011-1h4a1 1 0 011 1v-4a1 1 0 01-1-1H6a1 1 0 00-1-1v4a1 1 0 013 3h4a1 1 0 011 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white group-hover:text-accent transition-colors duration-200">Home</h4>
                            <p class="text-sm text-secondary">Return to dashboard</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('order') }}" class="block p-4 bg-card/30 rounded-xl hover-lift group cursor-pointer transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 success-bg rounded-lg flex items-center justify-center mr-3 group-hover:animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white group-hover:text-accent transition-colors duration-200">Services</h4>
                            <p class="text-sm text-secondary">Browse our services</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('client.tickets') }}" class="block p-4 bg-card/30 rounded-xl hover-lift group cursor-pointer transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 warning-bg rounded-lg flex items-center justify-center mr-3 group-hover:animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white group-hover:text-accent transition-colors duration-200">Support</h4>
                            <p class="text-sm text-secondary">Get help from our team</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-in" style="animation-delay: 0.6s">
            <a href="{{ route('home') }}" class="enhanced-button accent-bg hover:opacity-90 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7 7 7 7M5 10v10a1 1 0 011-1h4a1 1 0 011 1v-4a1 1 0 01-1-1H6a1 1 0 00-1-1v4a1 1 0 013 3h4a1 1 0 011 1z"/>
                </svg>
                Go Home
            </a>
            
            <button onclick="history.back()" class="enhanced-button bg-card/80 hover:bg-card text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m14 14l7-7M3 10h18a9 9 0 011-18 0 9 9 0 0118 0z"/>
                </svg>
                Go Back
            </button>
        </div>
    </div>
</div>

<script>
// Add interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Create floating particles
    const particles = document.querySelectorAll('.particle');
    particles.forEach((particle, index) => {
        particle.style.animationDelay = `${index * 0.5}s`;
    });
    
    // Add mouse parallax effect
    document.addEventListener('mousemove', (e) => {
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        
        document.querySelectorAll('.animate-float').forEach(element => {
            element.style.transform = `translateY(${-20 + y * 10}px) rotate(${x * 2}deg)`;
        });
    });
});
</script>
@endsection
