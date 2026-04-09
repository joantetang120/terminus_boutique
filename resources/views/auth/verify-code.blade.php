<x-auth-layout title="Vérification du code">
    <div class="login-page">
        {{-- LEFT — Decorative Panel --}}
        <div class="login-decoration">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="deco-content">
                <div class="deco-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h1>Terminus-Boutique</h1>
                <p class="deco-subtitle">Gestion de boutique simplifiée</p>
                <div class="deco-features">
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Code valide 10 minutes
                    </div>
                    <div class="deco-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        Vérification en 4 chiffres
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT — Form Panel --}}
        <div class="login-form-panel">
            <div class="login-form-card">
                <div class="form-header">
                    <div style="width:48px;height:48px;border-radius:12px;background:#FEF5EC;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#E67E22"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                    </div>
                    <h2>Vérification du code</h2>
                    <p>Entrez le code à 4 chiffres envoyé à<br><strong>{{ $email }}</strong></p>
                </div>

                <form action="{{ route('password.verify.post') }}" method="POST" id="verify-form">
                    @csrf

                    {{-- 4 Digit Input Boxes — pure HTML/JS, no x-model --}}
                    <div class="form-group" style="text-align:center;">
                        <div style="display:flex;justify-content:center;gap:12px;margin-bottom:16px;" id="code-inputs">
                            <input type="text" inputmode="numeric" maxlength="1"
                                   class="form-input code-input"
                                   style="width:64px;height:72px;text-align:center;font-size:1.75rem;font-weight:700;padding:0;"
                                   id="code-0" autocomplete="one-time-code">
                            <input type="text" inputmode="numeric" maxlength="1"
                                   class="form-input code-input"
                                   style="width:64px;height:72px;text-align:center;font-size:1.75rem;font-weight:700;padding:0;"
                                   id="code-1">
                            <input type="text" inputmode="numeric" maxlength="1"
                                   class="form-input code-input"
                                   style="width:64px;height:72px;text-align:center;font-size:1.75rem;font-weight:700;padding:0;"
                                   id="code-2">
                            <input type="text" inputmode="numeric" maxlength="1"
                                   class="form-input code-input"
                                   style="width:64px;height:72px;text-align:center;font-size:1.75rem;font-weight:700;padding:0;"
                                   id="code-3">
                        </div>

                        {{-- Hidden input updated by JS for form submission --}}
                        <input type="hidden" name="code" id="hidden-code">
                    </div>

                    @error('code')
                    <div class="form-error" style="text-align:center;margin-bottom:12px;">{{ $message }}</div>
                    @enderror

                    @if(session('success'))
                    <div class="flash flash-success" style="text-align:center;margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        {{ session('success') }}
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary" id="btn-verify" style="width:100%;height:48px;font-size:0.9375rem;opacity:0.5;cursor:not-allowed;" disabled>
                        Vérifier le code
                    </button>
                </form>

                {{-- Resend form - separate from verify form --}}
                <form action="{{ route('password.resend') }}" method="POST" style="text-align:center;margin-bottom:16px;" x-data="{ canResend: false, countdown: 60, timer: null, get countdownText() { const m = Math.floor(this.countdown / 60); const s = this.countdown % 60; return m + ':' + s.toString().padStart(2, '0'); }, init() { this.startTimer(); }, startTimer() { this.canResend = false; this.countdown = 60; if (this.timer) clearInterval(this.timer); this.timer = setInterval(() => { if (this.countdown > 0) { this.countdown--; } else { this.canResend = true; clearInterval(this.timer); } }, 1000); } }">
                    <div style="font-size:0.8125rem;color:#64748B;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Renvoyer dans</span>
                        <strong x-text="countdownText" style="color:#1B3A6B;font-size:0.875rem;"></strong>
                    </div>
                    @csrf
                    <button type="submit"
                            class="btn-resend"
                            id="btn-resend"
                            x-bind:disabled="!canResend"
                            x-bind:style="!canResend ? 'opacity:0.4;cursor:not-allowed;pointer-events:none;' : ''">
                        🔄 Renvoyer un nouveau code
                    </button>
                </form>

                <p style="text-align:center;margin-top:24px;font-size:0.875rem;">
                    <a href="{{ route('login') }}" style="color:#2E75B6;text-decoration:none;font-weight:500;">
                        ← Retour à la connexion
                    </a>
                </p>
            </div>
        </div>
    </div>

    <style>
        .code-input {
            border: 2px solid #E2E8F0;
            transition: all 150ms;
        }
        .code-input:focus {
            border-color: #1B3A6B;
            box-shadow: 0 0 0 3px rgba(27,58,107,0.15);
            outline: none;
        }
        .code-input.filled {
            border-color: #1B3A6B;
            background: #F8FAFC;
        }
        .btn-resend {
            background: none;
            border: none;
            color: #2E75B6;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            padding: 8px 16px;
            font-family: inherit;
            border-radius: 8px;
            transition: all 150ms;
        }
        .btn-resend:hover:not(:disabled) {
            color: #1B3A6B;
            background: #EBF4FF;
        }
        .btn-resend:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>

    <script>
        // === Pure JS input handling ===
        const inputs = [
            document.getElementById('code-0'),
            document.getElementById('code-1'),
            document.getElementById('code-2'),
            document.getElementById('code-3'),
        ];
        const hiddenCode = document.getElementById('hidden-code');
        const btnVerify = document.getElementById('btn-verify');

        function updateHidden() {
            const code = inputs.map(i => i.value).join('');
            hiddenCode.value = code;
            console.log('Code updated:', code, 'Length:', code.length);

            // Enable/disable verify button
            if (code.length === 4) {
                btnVerify.disabled = false;
                btnVerify.style.opacity = '1';
                btnVerify.style.cursor = 'pointer';
                console.log('Button ENABLED');
            } else {
                btnVerify.disabled = true;
                btnVerify.style.opacity = '0.5';
                btnVerify.style.cursor = 'not-allowed';
                console.log('Button DISABLED');
            }

            // Visual filled state
            inputs.forEach(inp => {
                if (inp.value) inp.classList.add('filled');
                else inp.classList.remove('filled');
            });
        }

        inputs.forEach((inp, idx) => {
            inp.addEventListener('input', function() {
                // Keep only digits
                this.value = this.value.replace(/[^0-9]/g, '');
                updateHidden();

                // Auto-advance
                if (this.value !== '' && idx < 3) {
                    inputs[idx + 1].focus();
                }
            });

            inp.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    e.preventDefault();
                    if (this.value !== '') {
                        this.value = '';
                        updateHidden();
                    } else if (idx > 0) {
                        inputs[idx - 1].value = '';
                        inputs[idx - 1].focus();
                        updateHidden();
                    }
                }
            });

            inp.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').trim();
                const nums = paste.replace(/[^0-9]/g, '').split('').slice(0, 4);
                nums.forEach((n, i) => {
                    if (inputs[i]) inputs[i].value = n;
                });
                updateHidden();
                const focusIdx = Math.min(nums.length, 3);
                if (inputs[focusIdx]) inputs[focusIdx].focus();
            });
        });

        // Focus first on load
        setTimeout(() => { if (inputs[0]) inputs[0].focus(); }, 100);

        // Debug form submission
        document.getElementById('verify-form').addEventListener('submit', function(e) {
            console.log('Form submitting, hidden code value:', hiddenCode.value);
        });

        // Debug button clicks
        btnVerify.addEventListener('click', function(e) {
            console.log('Button CLICKED, disabled state:', this.disabled);
        });
    </script>
</x-auth-layout>
