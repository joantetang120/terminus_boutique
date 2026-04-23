<x-app-layout title="Vérification du profil">
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-header">
                <div class="verify-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h2 class="verify-title">Vérification de sécurité</h2>
                <p class="verify-subtitle">
                    @if($email_changed)
                        Code envoyé à votre nouvelle adresse
                    @else
                        Code envoyé à votre email
                    @endif
                </p>
            </div>

            <div class="verify-body">
                @if(session('success'))
                    <div class="success-message">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-message">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Changes Summary --}}
                @if(!empty($changes))
                    <div class="changes-summary">
                        <p class="changes-title">Modifications demandées :</p>
                        <ul class="changes-list">
                            @if(isset($changes['name']))
                                <li class="change-item">
                                    <span class="change-label">Nom</span>
                                    <span class="change-arrow">→</span>
                                    <span class="change-new">{{ $changes['name']['new'] }}</span>
                                </li>
                            @endif
                            @if(isset($changes['email']))
                                <li class="change-item highlight">
                                    <span class="change-label">Email</span>
                                    <span class="change-arrow">→</span>
                                    <span class="change-new">{{ $changes['email']['new'] }}</span>
                                </li>
                            @endif
                            @if(isset($changes['password']))
                                <li class="change-item">
                                    <span class="change-label">Mot de passe</span>
                                    <span class="change-arrow">→</span>
                                    <span class="change-new">••••••••</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                <p class="code-instruction">
                    Saisissez le code à <strong>4 chiffres</strong> reçu par email
                </p>

                @if(isset($recipient_email))
                    <p class="recipient-email" title="{{ $recipient_email }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        {{ Str::limit($recipient_email, 25) }}
                    </p>
                @endif

                <form action="{{ route('profile.confirm_update') }}" method="POST" id="verifyForm">
                    @csrf

                    <div class="otp-container">
                        <input type="text" name="code[]" maxlength="1" class="otp-input" data-index="0" required>
                        <input type="text" name="code[]" maxlength="1" class="otp-input" data-index="1" required>
                        <input type="text" name="code[]" maxlength="1" class="otp-input" data-index="2" required>
                        <input type="text" name="code[]" maxlength="1" class="otp-input" data-index="3" required>
                    </div>

                    <input type="hidden" name="code" id="fullCode">

                    <button type="submit" class="verify-btn" id="verifyBtn" disabled>
                        <span>Confirmer la mise à jour</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                </form>

                <a href="{{ route('profile.show') }}" class="cancel-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Annuler et retourner au profil
                </a>
            </div>
        </div>
    </div>

    <style>
        .verify-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .verify-card {
            max-width: 420px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .verify-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 35px 30px;
            text-align: center;
            color: white;
        }

        .verify-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
        }

        .verify-title {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 700;
        }

        .verify-subtitle {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .verify-body {
            padding: 35px 30px;
        }

        .success-message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #dcfce7;
            color: #166534;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .changes-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }

        .changes-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 12px 0;
        }

        .changes-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .change-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .change-item:last-child {
            border-bottom: none;
        }

        .change-item.highlight {
            background: #fef3c7;
            margin: 0 -10px;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #fcd34d;
        }

        .change-label {
            font-weight: 600;
            color: #475569;
            min-width: 80px;
        }

        .change-arrow {
            color: #94a3b8;
        }

        .change-new {
            color: #1e293b;
            font-weight: 500;
        }

        .code-instruction {
            text-align: center;
            color: #475569;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .code-instruction strong {
            color: #1e293b;
        }

        .recipient-email {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        .otp-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .otp-input {
            width: 60px;
            height: 70px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s ease;
            outline: none;
        }

        .otp-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            transform: scale(1.05);
        }

        .otp-input.filled {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .verify-btn {
            width: 100%;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .verify-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 41, 59, 0.3);
        }

        .verify-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .cancel-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.9rem;
            text-decoration: none;
            margin-top: 25px;
            transition: color 0.2s;
        }

        .cancel-link:hover {
            color: #475569;
        }

        @media (max-width: 480px) {
            .otp-input {
                width: 50px;
                height: 60px;
                font-size: 1.5rem;
            }

            .verify-header {
                padding: 25px 20px;
            }

            .verify-body {
                padding: 25px 20px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const fullCodeInput = document.getElementById('fullCode');
            const verifyBtn = document.getElementById('verifyBtn');
            const form = document.getElementById('verifyForm');

            // Focus first input on load
            inputs[0].focus();

            inputs.forEach((input, index) => {
                // Only allow numbers
                input.addEventListener('keypress', function(e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                });

                input.addEventListener('input', function(e) {
                    const value = this.value;

                    if (value.length === 1) {
                        this.classList.add('filled');
                        // Move to next input
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    } else {
                        this.classList.remove('filled');
                    }

                    updateFullCode();
                });

                // Handle backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                        inputs[index - 1].classList.remove('filled');
                        updateFullCode();
                    }
                });

                // Paste handling
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 4);

                    inputs.forEach((inp, i) => {
                        if (pastedData[i]) {
                            inp.value = pastedData[i];
                            inp.classList.add('filled');
                        }
                    });

                    // Focus the appropriate input
                    const nextEmpty = Array.from(inputs).findIndex(inp => inp.value === '');
                    if (nextEmpty !== -1) {
                        inputs[nextEmpty].focus();
                    } else {
                        inputs[inputs.length - 1].focus();
                    }

                    updateFullCode();
                });
            });

            function updateFullCode() {
                const code = Array.from(inputs).map(input => input.value).join('');
                fullCodeInput.value = code;

                // Enable/disable button based on complete code
                verifyBtn.disabled = code.length !== 4;
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                const code = fullCodeInput.value;
                if (code.length !== 4) {
                    e.preventDefault();
                    return;
                }

                // Disable button during submission
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<span>Vérification...</span>';
            });
        });
    </script>
</x-app-layout>