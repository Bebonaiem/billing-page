/* ===================================================
   BillingHub - Premium Interactive JavaScript
   =================================================== */

document.addEventListener('DOMContentLoaded', () => {

    // ---------- Custom Cursor Trail ----------
    const cursorTrail = document.getElementById('cursor-trail');
    if (cursorTrail) {
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            cursorTrail.style.opacity = '1';
        });
        
        document.addEventListener('mouseleave', () => {
            cursorTrail.style.opacity = '0';
        });
        
        function animateCursor() {
            const dx = mouseX - cursorX;
            const dy = mouseY - cursorY;
            
            cursorX += dx * 0.1;
            cursorY += dy * 0.1;
            
            cursorTrail.style.transform = `translate(${cursorX - 10}px, ${cursorY - 10}px)`;
            
            requestAnimationFrame(animateCursor);
        }
        animateCursor();
    }

    // ---------- Navbar Scroll Effect ----------
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        const handleScroll = () => {
            const scrollY = window.scrollY;
            if (scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    }

    // ---------- Mobile Menu Toggle ----------
    const navToggle = document.querySelector('.nav-toggle');
    const navMobile = document.querySelector('.nav-mobile');

    if (navToggle && navMobile) {
        navToggle.addEventListener('click', () => {
            navToggle.classList.toggle('active');
            navMobile.classList.toggle('open');
            document.body.style.overflow = navMobile.classList.contains('open') ? 'hidden' : '';
        });

        navMobile.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navToggle.classList.remove('active');
                navMobile.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    }

    // ---------- Header Search Functionality ----------
    const topnavSearchInput = document.querySelector('.topnav-search input');
    const topnavSearch = document.querySelector('.topnav-search');

    if (topnavSearchInput && topnavSearch) {
        // Keyboard shortcut: Press 'K' to focus search
        document.addEventListener('keydown', (e) => {
            if ((e.key === 'k' || e.key === 'K') && !e.ctrlKey && !e.metaKey) {
                // Don't trigger if user is typing in an input/textarea
                if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
                    return;
                }
                e.preventDefault();
                topnavSearchInput.focus();
            }
            // Escape to blur search
            if (e.key === 'Escape' && document.activeElement === topnavSearchInput) {
                topnavSearchInput.blur();
            }
        });

        // Focus/blur visual feedback
        topnavSearchInput.addEventListener('focus', () => {
            topnavSearch.classList.add('focused');
        });
        topnavSearchInput.addEventListener('blur', () => {
            topnavSearch.classList.remove('focused');
        });

        // Enter key: redirect to docs with search parameter if NOT on docs page
        topnavSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = topnavSearchInput.value.trim();
                const isDocsPage = document.getElementById('docs-sidebar') !== null;

                if (!isDocsPage && query) {
                    window.location.href = `docs.html?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }

    // ---------- Canvas Particle System ----------
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        const canvas = document.getElementById('hero-particles');
        if (canvas) {
            canvas.style.cssText = 'position:absolute;inset:0;z-index:1;pointer-events:none;';
            const ctx = canvas.getContext('2d');
            let particles = [];
            let animationId;
            let mouseX = 0, mouseY = 0;

            function resizeCanvas() {
                canvas.width = heroSection.offsetWidth;
                canvas.height = heroSection.offsetHeight;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            class Particle {
                constructor() {
                    this.reset();
                }
                reset() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2 + 0.5;
                    this.speedX = (Math.random() - 0.5) * 0.3;
                    this.speedY = (Math.random() - 0.5) * 0.3;
                    this.opacity = Math.random() * 0.5 + 0.1;
                    this.hue = Math.random() > 0.5 ? '#00E5DF' : '#00ff88'; 
                    this.pulse = Math.random() * Math.PI * 2;
                }
                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;
                    this.pulse += 0.02;
                    this.opacity = (Math.sin(this.pulse) * 0.3 + 0.3);

                    // Mouse interaction
                    const dx = mouseX - this.x;
                    const dy = mouseY - this.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        this.x -= dx * 0.01;
                        this.y -= dy * 0.01;
                    }

                    if (this.x < 0 || this.x > canvas.width || this.y < 0 || this.y > canvas.height) {
                        this.reset();
                    }
                }
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fillStyle = this.hue;
                    ctx.globalAlpha = this.opacity;
                    ctx.fill();

                    // Glow
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size * 3, 0, Math.PI * 2);
                    ctx.fillStyle = this.hue;
                    ctx.globalAlpha = this.opacity * 0.1;
                    ctx.fill();
                    ctx.globalAlpha = 1.0;
                }
            }

            // Create particles
            const particleCount = Math.min(60, Math.floor(window.innerWidth / 20));
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }

            // Draw connections
            function drawConnections() {
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 150) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(0, 255, 136, ${0.06 * (1 - dist / 150)})`;
                            ctx.lineWidth = 0.5;
                            ctx.stroke();
                        }
                    }
                }
            }

            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(p => {
                    p.update();
                    p.draw();
                });
                drawConnections();
                animationId = requestAnimationFrame(animateParticles);
            }

            animateParticles();

            heroSection.addEventListener('mousemove', (e) => {
                const rect = heroSection.getBoundingClientRect();
                mouseX = e.clientX - rect.left;
                mouseY = e.clientY - rect.top;
            });
        }
    }

    // ---------- Scroll Reveal Animation ----------
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -60px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
    }

    // ---------- Animated Counter ----------
    const counterElements = document.querySelectorAll('[data-count]');
    if (counterElements.length > 0) {
        const animateCounter = (el) => {
            const target = parseInt(el.getAttribute('data-count'), 10);
            const suffix = el.getAttribute('data-suffix') || '';
            const prefix = el.getAttribute('data-prefix') || '';
            const duration = 2000;
            const startTime = performance.now();

            const easeOutQuart = t => 1 - Math.pow(1 - t, 4);

            const update = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = easeOutQuart(progress);
                const current = Math.floor(target * easedProgress);

                el.textContent = prefix + current.toLocaleString() + suffix;

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            };

            requestAnimationFrame(update);
        };

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counterElements.forEach(el => counterObserver.observe(el));
    }

    // ---------- Feature Card Mouse Glow ----------
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            card.style.setProperty('--mouse-x', x + '%');
            card.style.setProperty('--mouse-y', y + '%');

            // Subtle 3D tilt
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (e.clientY - rect.top - centerY) / centerY * -2;
            const rotateY = (e.clientX - rect.left - centerX) / centerX * 2;
            card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    // ---------- Magnetic Buttons ----------
    document.querySelectorAll('.btn-primary, .download-btn-lg').forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.transform = '';
            btn.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => { btn.style.transition = ''; }, 400);
        });
    });

    // ---------- Parallax on Hero Orbs ----------
    const heroOrbs = document.querySelectorAll('.hero-orb');
    if (heroOrbs.length > 0) {
        window.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 2;
            const y = (e.clientY / window.innerHeight - 0.5) * 2;

            heroOrbs.forEach((orb, i) => {
                const speed = (i + 1) * 10;
                orb.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
            });
        }, { passive: true });
    }

    // ---------- Code Block Copy Button ----------
    document.querySelectorAll('pre').forEach(pre => {
        // Skip if copy button already exists (in pre or parent container)
        const container = pre.closest('.code-block, .code-tab-panel');
        if (pre.querySelector('.copy-btn') || (container && container.querySelector('.copy-btn'))) return;

        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.textContent = 'Copy';

        // Append to parent container, not inside pre (to avoid scrolling with code)
        if (container) {
            container.style.position = 'relative';
            container.appendChild(copyBtn);
        } else {
            pre.appendChild(copyBtn);
        }

        copyBtn.addEventListener('click', () => {
            const code = pre.querySelector('code');
            if (code) {
                navigator.clipboard.writeText(code.textContent).then(() => {
                    copyBtn.textContent = 'Copied!';
                    copyBtn.style.color = '#00ff88';
                    setTimeout(() => {
                        copyBtn.textContent = 'Copy';
                        copyBtn.style.color = '';
                    }, 2000);
                }).catch(() => {
                    const range = document.createRange();
                    range.selectNodeContents(code);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                    document.execCommand('copy');
                    selection.removeAllRanges();
                    copyBtn.textContent = 'Copied!';
                    setTimeout(() => { copyBtn.textContent = 'Copy'; }, 2000);
                });
            }
        });
    });

    // ---------- Smooth Scroll for Anchor Links ----------
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const targetId = anchor.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // ---------- Text Scramble Effect for Badge ----------
    const badgeText = document.querySelector('.badge-text[data-glitch]');
    if (badgeText) {
        const originalText = badgeText.textContent.trim();
        const chars = '!@#$%^&*()_+-=[]{}|;:,./<>?';

        function scrambleText(el, text) {
            let iteration = 0;
            const interval = setInterval(() => {
                el.textContent = text
                    .split('')
                    .map((char, index) => {
                        if (index < iteration) return text[index];
                        return chars[Math.floor(Math.random() * chars.length)];
                    })
                    .join('');
                if (iteration >= text.length) clearInterval(interval);
                iteration += 1 / 2;
            }, 30);
        }

        setTimeout(() => scrambleText(badgeText, originalText), 600);
    }

    // ---------- Pricing Toggle ----------
    const pricingToggle = document.querySelector('.pricing-toggle');
    if (pricingToggle) {
        pricingToggle.addEventListener('change', (e) => {
            const grid = document.querySelector('.pricing-grid');
            if(e.target.checked) {
                grid.classList.add('annual');
            } else {
                grid.classList.remove('annual');
            }
        });
    }

    // ---------- Showcase Card Hover Animation Add-on ----------
    document.querySelectorAll('.showcase-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px) scale(1.03)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

});