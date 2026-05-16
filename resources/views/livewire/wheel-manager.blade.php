<div>
    <!-- Ambient Magical Environment (Full Screen) -->
    <div class="magical-environment-fixed">
        <div class="vignette-overlay"></div>
        <div class="dust-container">
            @for($m = 0; $m < 35; $m++)
                <div class="magical-dust" style="--x: {{ rand(0, 100) }}vw; --x2: {{ rand(0, 100) }}vw; --d: {{ rand(15, 40) }}s; --s: {{ rand(2, 6) }}px; left: 0; top: 0;"></div>
            @endfor
        </div>
        <div class="mystic-runes-bg"></div>
    </div>

    <div class="wheel-manager-container integrated-page-container">
        <!-- Header Section -->
        <div class="page-header-mystic">
            <div class="title-section">
                <h1 class="wheel-title-main">Gerenciador de Rodas</h1>
                <div class="magical-gradient-divider"></div>
            </div>
            <button wire:click="openModal" class="btn-magical-seal">
                <div class="seal-glow"></div>
                <div class="seal-inner">
                    <span class="seal-sparkle">✨</span>
                    <span class="seal-text">NOVA RODA</span>
                </div>
            </button>
        </div>

        @if (session()->has('message'))
            <div class="alert-magical">
                <span class="alert-spark">✨</span>
                {{ session('message') }}
            </div>
        @endif

        <!-- Grid of Organic Cards -->
        <div class="wheels-grid-mystic">
            @forelse ($wheels as $wheel)
                <a href="{{ route('wheel.details', $wheel->id) }}" class="wheel-card-link">
                    <div class="wheel-card-organic {{ $selectedWheelId == $wheel->id ? 'selected' : '' }}">
                        <div class="card-aura"></div>
                        
                        <div class="wheel-visual-organic">
                            <div class="magical-wheel-chart-mini float-anim">
                                <svg viewBox="0 0 100 100" class="magical-wheel-svg">
                                    @php
                                        $colors = [
                                            1 => '#d4af37', 2 => '#c4302b', 3 => '#2d5a27', 4 => '#1a237e', 
                                            5 => '#6a1b9a', 6 => '#fbc02d', 7 => '#e64a19', 8 => '#00838f', 
                                            9 => '#ad1457', 10 => '#ffffff'
                                        ];
                                    @endphp
                                    @for($i = 0; $i < 10; $i++)
                                        @php
                                            $levelNum = $i + 1;
                                            $isActive = $levelNum <= $wheel->level;
                                            $startAngle = $i * 36;
                                            $endAngle = ($i + 1) * 36 - 4;
                                            $r1 = 35; $r2 = 48;
                                            $x1 = 50 + $r1 * cos(deg2rad($startAngle - 90)); $y1 = 50 + $r1 * sin(deg2rad($startAngle - 90));
                                            $x2 = 50 + $r2 * cos(deg2rad($startAngle - 90)); $y2 = 50 + $r2 * sin(deg2rad($startAngle - 90));
                                            $x3 = 50 + $r2 * cos(deg2rad($endAngle - 90)); $y3 = 50 + $r2 * sin(deg2rad($endAngle - 90));
                                            $x4 = 50 + $r1 * cos(deg2rad($endAngle - 90)); $y4 = 50 + $r1 * sin(deg2rad($endAngle - 90));
                                        @endphp
                                        <path d="M {{ $x1 }} {{ $y1 }} L {{ $x2 }} {{ $y2 }} A {{ $r2 }} {{ $r2 }} 0 0 1 {{ $x3 }} {{ $y3 }} L {{ $x4 }} {{ $y4 }} A {{ $r1 }} {{ $r1 }} 0 0 0 {{ $x1 }} {{ $y1 }}" 
                                              fill="{{ $isActive ? $colors[$levelNum] : 'rgba(0,0,0,0.05)' }}" 
                                              stroke="{{ $isActive ? 'rgba(212,175,55,0.3)' : 'rgba(0,0,0,0.1)' }}"
                                              stroke-width="0.3"
                                              class="wheel-segment {{ $isActive ? 'active' : '' }}" />
                                    @endfor
                                    <text x="50" y="58" font-family="Cinzel, serif" font-size="24" font-weight="900" text-anchor="middle" fill="var(--gold-color)" class="level-center-text">{{ $wheel->level }}</text>
                                </svg>
                            </div>
                        </div>

                        <div class="card-content-organic">
                            <h3 class="wheel-name-organic">{{ $wheel->name }}</h3>
                            <div class="level-title-badge">{{ $wheel->level_title }}</div>
                            
                            <div class="ink-underline-organic"></div>
                            <p class="wheel-desc-organic">{{ Illuminate\Support\Str::limit($wheel->description, 80) }}</p>
                            
                            <div class="wheel-xp-bar-container">
                                <div class="wheel-xp-bar-fill" style="width: {{ $wheel->level_progress_percentage }}%"></div>
                                <span class="xp-percentage">{{ round($wheel->level_progress_percentage) }}%</span>
                            </div>

                            <div class="wheel-meta-organic">
                                <div class="meta-item">
                                    <span class="meta-label">Feitiços</span>
                                    <span class="meta-value">✨ {{ $wheel->spells->count() }}</span>
                                </div>
                                <div class="meta-divider"></div>
                                <div class="meta-item">
                                    <span class="meta-label">Missões</span>
                                    <span class="meta-value">📜 {{ $wheel->quests->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state-mystic">
                    <div class="empty-icon-parchment">📜</div>
                    <p class="empty-msg">O seu destino aguarda ser escrito.</p>
                    <button wire:click="openModal" class="btn-magical-seal">CONJURAR RODA</button>
                </div>
            @endforelse
        </div>

        <!-- Modal System -->
        @if($showModal)
            <div class="modal-backdrop-mystic" wire:click="closeModal">
                <div class="modal-parchment" wire:click.stop>
                    <div class="parchment-texture"></div>
                    <div class="modal-header-mystic">
                        <h2>{{ $isEditing ? 'Reescrever Destino' : 'Conjurar Nova Roda' }}</h2>
                        <button wire:click="closeModal" class="close-btn-mystic">✕</button>
                    </div>
                    <div class="modal-body-mystic">
                        <form wire:submit.prevent="save">
                            <div class="form-group-parchment">
                                <label>Nome da Roda</label>
                                <input type="text" wire:model="name" class="input-parchment" placeholder="Nomeie sua criação...">
                                @error('name') <span class="error-magical">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group-parchment">
                                <label>Descrição</label>
                                <textarea wire:model="description" class="input-parchment" placeholder="Descreva o propósito desta roda..."></textarea>
                                @error('description') <span class="error-magical">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group-parchment">
                                <label>Vincular Feitiços</label>
                                <div class="spell-select-parchment">
                                    @forelse($all_spells as $spell)
                                        <label class="spell-option">
                                            <input type="checkbox" wire:model="selected_spells" value="{{ $spell->id }}">
                                            <span class="spell-name">{{ $spell->name }}</span>
                                            <span class="spell-wheel-tag">@if($spell->wheel) ({{ $spell->wheel->name }}) @endif</span>
                                        </label>
                                    @empty
                                        <p class="no-spells">Nenhum feitiço no grimório.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="modal-actions-parchment">
                                <button type="submit" class="btn-magical-seal">
                                    <span class="seal-text">{{ $isEditing ? 'ATUALIZAR' : 'CONJURAR' }}</span>
                                </button>
                                <button type="button" wire:click="closeModal" class="btn-text-parchment">CANCELAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        :root {
            --gold-color: #d4af37;
            --ink-color: #2c1810;
            --parchment-light: rgba(252, 248, 237, 0.4);
            --parchment-glow: rgba(252, 248, 237, 0.9);
        }

        .integrated-page-container {
            max-width: 100% !important;
            margin: -2rem !important;
            padding: 2rem !important;
            background: var(--page-bg-final) !important;
            min-height: calc(100vh - 60px);
            position: relative;
            z-index: 1;
        }

        /* Magical Environment (Full Screen Fixed) */
        .magical-environment-fixed {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .vignette-overlay {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, transparent 40%, rgba(0,0,0,0.06) 100%);
        }
        .magical-dust {
            position: absolute;
            width: var(--s); height: var(--s);
            background: var(--gold-color);
            border-radius: 50%;
            opacity: 0.15;
            filter: blur(1px);
            animation: floatMote var(--d) infinite linear;
        }
        @keyframes floatMote {
            0% { transform: translate(var(--x), 110vh) scale(0); opacity: 0; }
            50% { opacity: 0.3; }
            100% { transform: translate(var(--x2), -10vh) scale(1.5); opacity: 0; }
        }
        .mystic-runes-bg {
            position: absolute;
            inset: 0;
            opacity: 0.03;
            background-image: url('https://www.transparenttextures.com/patterns/pinstriped-suit.png');
            pointer-events: none;
        }

        /* Header Styling */
        .page-header-mystic {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            position: relative;
            z-index: 10;
        }
        .wheel-title-main {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            color: var(--ink-color);
            margin: 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .magical-gradient-divider {
            width: 100%;
            max-width: 200px;
            height: 2px;
            margin-top: 4px;
            background: linear-gradient(90deg, transparent, var(--gold-color), transparent);
            opacity: 0.5;
        }

        /* Magical Seal Button */
        .btn-magical-seal {
            background: #8b0000;
            color: var(--gold-color);
            border: 2px solid var(--gold-color);
            border-radius: 4rem;
            padding: 0.7rem 1.8rem;
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            font-weight: bold;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: 0.4s;
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
        }
        .btn-magical-seal:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 0, 0, 0.5);
        }
        .seal-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.2) 0%, transparent 70%);
            animation: pulseGlow 2s infinite;
        }
        .seal-inner { position: relative; z-index: 2; display: flex; align-items: center; gap: 0.5rem; }

        /* Compact Organic Cards */
        .wheels-grid-mystic {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            position: relative;
            z-index: 5;
        }
        .wheel-card-link {
            text-decoration: none !important;
            color: inherit;
        }
        .wheel-card-organic {
            background: var(--parchment-light);
            border: 1px solid rgba(44, 24, 16, 0.08);
            border-radius: 1.5rem 3.5rem 1.5rem 3.5rem;
            padding: 1.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            min-height: 360px;
            backdrop-filter: blur(1px);
        }
        .wheel-card-organic:hover {
            background: var(--parchment-glow);
            border-color: var(--gold-color);
            transform: translateY(-8px) rotate(0.5deg);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .wheel-visual-organic { margin-bottom: 1rem; }
        .magical-wheel-chart-mini { width: 130px; height: 130px; }
        .float-anim { animation: floatIntegrated 6s infinite ease-in-out; }
        @keyframes floatIntegrated {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .card-content-organic { text-align: center; width: 100%; display: flex; flex-direction: column; flex-grow: 1; }
        .wheel-name-organic {
            font-family: 'Cinzel', serif;
            font-size: 1.1rem;
            color: var(--ink-color);
            margin: 0;
            letter-spacing: 0.5px;
        }
        .level-title-badge {
            font-family: 'Spectral', serif;
            font-size: 0.75rem;
            color: var(--gold-color);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1px;
            opacity: 0.8;
        }
        .ink-underline-organic {
            height: 1px;
            width: 40%;
            background: var(--ink-color);
            margin: 6px auto 8px;
            opacity: 0.15;
            border-radius: 50%;
        }
        .wheel-desc-organic {
            font-family: 'Spectral', serif;
            font-size: 0.85rem;
            color: var(--ink-color);
            opacity: 0.65;
            font-style: italic;
            line-height: 1.3;
            margin-bottom: 1rem;
        }

        /* XP Progress Bar */
        .wheel-xp-bar-container {
            width: 100%;
            height: 4px;
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
            margin-bottom: 1.2rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.02);
        }
        .wheel-xp-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gold-color), #ffeb3b);
            border-radius: 10px;
            transition: width 1s ease-out;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }
        .xp-percentage {
            position: absolute;
            right: 0;
            top: -14px;
            font-size: 0.6rem;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            color: var(--gold-color);
        }

        .wheel-meta-organic {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            margin-top: auto;
        }
        .meta-item { display: flex; flex-direction: column; align-items: center; }
        .meta-label { font-family: 'Cinzel', serif; font-size: 0.6rem; opacity: 0.4; text-transform: uppercase; }
        .meta-value { font-family: 'Cinzel', serif; font-size: 0.95rem; font-weight: 900; color: var(--ink-color); }
        .meta-divider { width: 1px; height: 18px; background: rgba(0,0,0,0.08); }

        /* Modal Parchment */
        .modal-backdrop-mystic {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-parchment {
            background: #fdf5e6;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            border: 1px solid #d2b48c;
            animation: parchmentRoll 0.4s ease-out;
        }

        .modal-header-mystic { padding: 1.5rem; border-bottom: 1px dashed rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .modal-header-mystic h2 { font-family: 'Cinzel', serif; font-size: 1.2rem; margin: 0; color: var(--ink-color); }
        .close-btn-mystic { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--ink-color); opacity: 0.5; transition: 0.3s; }
        .close-btn-mystic:hover { opacity: 1; }

        .modal-body-mystic { padding: 1.5rem 2rem 2rem; }
        .form-group-parchment { margin-bottom: 1.5rem; }
        .form-group-parchment label {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            font-weight: bold;
            margin-bottom: 0.4rem;
            color: var(--ink-color);
        }
        .input-parchment {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.15);
            padding: 0.6rem 0;
            font-family: 'Spectral', serif;
            font-size: 1rem;
            color: var(--ink-color);
            transition: 0.3s;
        }
        .input-parchment:focus { outline: none; border-bottom-color: var(--gold-color); }

        .spell-select-parchment {
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid rgba(0,0,0,0.08);
            padding: 0.8rem;
            border-radius: 4px;
            background: rgba(0,0,0,0.01);
        }
        .spell-option { display: flex; align-items: center; gap: 0.8rem; padding: 0.4rem; cursor: pointer; }
        .spell-name { font-family: 'Spectral', serif; font-size: 0.9rem; color: var(--ink-color); }

        .modal-actions-parchment { display: flex; justify-content: flex-end; align-items: center; gap: 1.5rem; margin-top: 1.5rem; }
        .btn-text-parchment { background: none; border: none; font-family: 'Cinzel', serif; font-size: 0.75rem; cursor: pointer; opacity: 0.5; transition: 0.3s; }
        .btn-text-parchment:hover { opacity: 1; color: #8b0000; }

        .alert-magical {
            background: rgba(45, 90, 39, 0.08);
            border: 1px solid rgba(45, 90, 39, 0.2);
            color: #2d5a27;
            padding: 0.8rem 1.5rem;
            border-radius: 2rem;
            margin-bottom: 2rem;
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
    </style>
</div>
</div>