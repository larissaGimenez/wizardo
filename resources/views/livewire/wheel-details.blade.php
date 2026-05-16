<div class="wheel-details-container full-width">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="alert alert-success toast">
            <span>✨ {{ session('message') }}</span>
            <button @click="show = false" class="close-alert">&times;</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="alert alert-danger toast">
            <span>🚫 {{ session('error') }}</span>
            <button @click="show = false" class="close-alert">&times;</button>
        </div>
    @endif

    <div class="breadcrumb">
        <nav class="magical-breadcrumb">
            <a href="{{ route('wheel.manager') }}">Rodas</a>
            <span class="magical-arrow">›</span>
            <span class="current">Detalhes da Roda</span>
        </nav>
    </div>

    <!-- Header com Título e Rank -->
    <div class="details-header-main">
        <div class="header-flex-wrapper">
            <div class="level-badge-pill">
                <span class="level-num">Nv {{ $wheel->level }}</span>
                <span class="level-sep"></span>
                <span class="level-name">{{ $wheel->level_title }}</span>
            </div>
            
            <div class="name-edit-container">
                @if($isEditingName)
                    <div class="inline-edit">
                        <input type="text" wire:model="newName" wire:keydown.enter="confirmSave('name')" wire:keydown.escape="cancelEditing" wire:blur="confirmSave('name')" autofocus class="edit-input-title">
                    </div>
                @else
                    <h1 class="wheel-title clickable" wire:click="startEditingName">
                        {{ $wheel->name }}
                    </h1>
                @endif
            </div>
        </div>
    </div>

    <!-- Barra de Progresso XP -->
    <div class="xp-section-minimal">
        <div class="xp-bar-outer">
            <div class="xp-bar-inner" style="width: {{ $wheel->level_progress_percentage }}%"></div>
            <div class="xp-bar-text">
                {{ number_format($wheel->xp) }} / {{ number_format($wheel->xp_required_for_next_level) }} XP
            </div>
        </div>
        @if($wheel->xp >= $wheel->xp_required_for_next_level && $wheel->level < 10)
            <div class="xp-alert-glow">✨ META DE XP ATINGIDA! CONCLUA O DESAFIO PARA SUBIR.</div>
        @endif
    </div>

    <div class="details-three-columns-grid">
        
        <!-- Coluna 1: Gráfico de Progresso e Descrição -->
        <div class="column column-visual">
            <div class="integrated-visual-section">
                <div class="magical-wheel-chart-integrated">
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
                                  fill="{{ $isActive ? $colors[$levelNum] : 'var(--segment-inactive)' }}" 
                                  stroke="{{ $isActive ? 'rgba(212,175,55,0.2)' : 'var(--segment-stroke)' }}"
                                  stroke-width="0.3"
                                  class="wheel-segment {{ $isActive ? 'active' : '' }}" />
                        @endfor
                        <text x="50" y="55" font-family="Arial" font-size="20" text-anchor="middle" class="trophy-icon">🏆</text>
                    </svg>
                </div>
                
                <div class="info-box-integrated">
                    <h3 class="section-sub-title">Sobre esta Roda</h3>
                    @if($isEditingDescription)
                        <div class="inline-edit">
                            <textarea wire:model="newDescription" wire:keydown.enter="confirmSave('description')" wire:keydown.escape="cancelEditing" wire:blur="confirmSave('description')" autofocus class="edit-textarea-integrated"></textarea>
                        </div>
                    @else
                        <p class="wheel-description-text clickable" wire:click="startEditingDescription">
                            {{ $wheel->description ?? 'Clique para adicionar uma descrição...' }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Coluna 2: Feitiços e Missões -->
        <div class="column column-actions">
            <!-- Feitiços Diários -->
            <div class="section-item-list">
                <h2 class="magical-header">Feitiços Diários</h2>
                <hr class="magical-separator-section">
                <div class="items-vertical">
                    @forelse($spells->where('type', 'feitiço diário') as $spell)
                        @php $isDone = $wheel->isSpellCompletedToday($spell->id); @endphp
                        <div class="item-card-v3 {{ $isDone ? 'done' : 'daily' }} clickable" 
                             @if(!$isDone) wire:click="useSpell({{ $spell->id }})" @endif>
                            <div class="card-v3-header-row">
                                <div class="card-v3-title-group">
                                    <span class="card-v3-name-minimal">{{ $spell->name }}</span>
                                </div>
                                <div class="card-v3-meta-minimal">
                                    @if($isDone)
                                        <span class="card-v3-badge danger-badge-glow">Malfeito feito!</span>
                                    @else
                                        <div x-data="countdown()" x-init="init()" class="card-v3-timer-minimal">
                                            <span x-text="timeStr">--:--:--</span>
                                        </div>
                                        <div class="card-v3-xp-minimal">
                                            <span class="xp-gain-val">+{{ $spell->gain }}</span>
                                            <span class="xp-sep-val">|</span>
                                            <span class="xp-damage-val">-{{ $spell->damage }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($spell->action)
                                <p class="card-v3-desc-subtitle">{{ $spell->action }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="empty-text">Nenhum feitiço diário.</p>
                    @endforelse
                </div>
            </div>

            <!-- Penalidades -->
            <div class="section-item-list">
                <h2 class="magical-header">Penalidades das Trevas</h2>
                <hr class="magical-separator-section">
                <div class="items-vertical">
                    @forelse($spells->where('type', 'penalidade das trevas') as $spell)
                        <div class="item-card-v3 penalty clickable" wire:click="useSpell({{ $spell->id }})">
                            <div class="card-v3-header-row">
                                <div class="card-v3-title-group">
                                    <span class="card-v3-name-minimal">{{ $spell->name }}</span>
                                </div>
                                <div class="card-v3-meta-minimal">
                                    <span class="card-v3-xp-minimal danger">-{{ $spell->damage }} XP</span>
                                </div>
                            </div>
                            @if($spell->action)
                                <p class="card-v3-desc-subtitle">{{ $spell->action }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="empty-text">Nenhuma penalidade.</p>
                    @endforelse
                </div>
            </div>

            <!-- Missões -->
            <div class="section-item-list">
                <h2 class="magical-header">Missões de Hogsmead</h2>
                <hr class="magical-separator-section">
                <div class="items-vertical">
                    @forelse($quests as $quest)
                        <div class="item-card-v3 quest clickable" wire:click="completeQuest({{ $quest->id }})">
                            <div class="card-v3-header-row">
                                <div class="card-v3-title-group">
                                    <span class="card-v3-name-minimal">{{ $quest->name }}</span>
                                </div>
                                <div class="card-v3-meta-minimal">
                                    <span class="card-v3-xp-minimal info">+{{ $quest->gain }} XP</span>
                                </div>
                            </div>
                            @if($quest->description)
                                <p class="card-v3-desc-subtitle">{{ $quest->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="empty-text">Nenhuma missão.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Coluna 3: Desafios -->
        <div class="column column-challenges">
            <h2 class="magical-header header-challenges-v2">📜 Provas de Maestria</h2>
            <hr class="magical-separator-challenges">
            <div class="challenges-vertical-list">
                @forelse($challenges as $challenge)
                    @php
                        $isCompleted = $challenge->is_completed || $challenge->level <= $wheel->level;
                        $canTry = $challenge->level == $wheel->level + 1 && $wheel->xp >= $wheel->xp_required_for_next_level;
                    @endphp
                    <div class="challenge-card-v2 {{ $isCompleted ? 'completed' : ($canTry ? 'unlocked' : 'locked') }}" 
                         @if($canTry) wire:click="completeChallenge({{ $challenge->id }})" style="cursor: pointer;" @endif>
                        <div class="challenge-v2-header">
                            <span class="challenge-v2-level">Nível {{ $challenge->level }}</span>
                            <div class="challenge-v2-icon">
                                @if($isCompleted) ✅ @elseif($canTry) 🔓 @else 🔒 @endif
                            </div>
                        </div>
                        <span class="challenge-v2-name">{{ $challenge->name }}</span>
                        @if($challenge->description)
                            <p class="challenge-v2-desc">{{ $challenge->description }}</p>
                        @endif
                    </div>
                @empty
                    <p class="empty-text">Sem provas registradas.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <div class="danger-zone-footer">
        <hr class="magical-separator">
        <div class="footer-actions">
            <button wire:click="resetProgress" wire:confirm="Isso irá zerar seu nível, XP e desafios desta roda. Deseja continuar?" class="btn btn-warning btn-sm">
                🔄 Resetar Progresso
            </button>
            <button wire:click="confirmDelete" class="btn btn-danger btn-sm">
                🗑️ Banir esta Roda
            </button>
        </div>
    </div>

    <x-magical-modal 
        show="showConfirmModal" 
        title="✨ Selar Alteração?" 
        description="Deseja confirmar as mudanças em <strong>'{{ $wheel->name }}'</strong>?" 
        confirmAction="save" 
        cancelAction="cancelEditing"
        type="primary"
    />

    <x-magical-modal 
        show="showDeleteModal" 
        title="🔥 Banir Roda?" 
        description="Tem certeza que deseja excluir permanentemente a roda <strong>'{{ $wheel->name }}'</strong>?" 
        confirmAction="delete" 
        cancelAction="cancelEditing"
        type="danger"
    />

    <script>
        function countdown() {
            return {
                timeStr: '00:00:00',
                init() {
                    this.update();
                    setInterval(() => this.update(), 1000);
                },
                update() {
                    const now = new Date();
                    const midnight = new Date(now);
                    midnight.setHours(24, 0, 0, 0);
                    const diff = midnight - now;
                    if (diff <= 0) { this.timeStr = '00:00:00'; return; }
                    const hours = Math.floor(diff / 3600000);
                    const mins = Math.floor((diff % 3600000) / 60000);
                    const secs = Math.floor((diff % 60000) / 1000);
                    this.timeStr = [hours, mins, secs].map(v => v.toString().padStart(2, '0')).join(':');
                }
            }
        }
    </script>

    <style>
        :root {
            --segment-inactive: rgba(0, 0, 0, 0.03);
            --segment-stroke: rgba(0, 0, 0, 0.05);
            --card-v3-bg: rgba(255, 255, 255, 0.5);
            --card-v3-hover: rgba(255, 255, 255, 0.8);
            --timer-bg: rgba(116, 27, 27, 0.04);
            --xp-text-color: #1a1a1a;
            --danger-badge: #741b1b;
        }

        [data-theme="dark"] {
            --segment-inactive: rgba(255, 255, 255, 0.03);
            --segment-stroke: rgba(255, 255, 255, 0.05);
            --card-v3-bg: rgba(255, 255, 255, 0.03);
            --card-v3-hover: rgba(255, 255, 255, 0.07);
            --timer-bg: rgba(212, 175, 55, 0.1);
            --xp-text-color: #fef3c7;
            --danger-badge: #d4af37; /* Em dark mode pode ser ouro ou um vermelho mais vivo */
        }

        .wheel-details-container.full-width { max-width: 1400px; margin: 0 auto; padding: 1rem; }
        .toast { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 3000; display: flex; align-items: center; gap: 0.8rem; padding: 0.8rem 1.2rem; border-radius: 0.8rem; border: 2px solid var(--gold-color); background: var(--card-bg); font-size: 0.9rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .magical-breadcrumb { font-family: 'Cinzel', serif; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; }
        .magical-breadcrumb a { color: var(--accent-color); text-decoration: none; }
        .magical-arrow { color: var(--accent-color); font-weight: bold; font-size: 1.1rem; opacity: 0.6; }
        .details-header-main { margin-bottom: 1.5rem; }
        .header-flex-wrapper { display: flex; align-items: center; gap: 1.5rem; }
        .level-badge-pill { background: var(--gold-color); color: #000; display: flex; align-items: center; border-radius: 2rem; overflow: hidden; font-family: 'Cinzel', serif; font-weight: bold; box-shadow: 0 0 15px rgba(212, 175, 55, 0.3); }
        .level-num { padding: 0.4rem 1rem; background: rgba(0,0,0,0.1); font-size: 1rem; }
        .level-name { padding: 0.4rem 1rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .wheel-title { font-family: 'Cinzel', serif; font-size: 2.2rem; margin: 0; color: var(--text-color); line-height: 1; letter-spacing: 1px; }
        .xp-section-minimal { margin-bottom: 3rem; }
        .xp-bar-outer { background: rgba(0,0,0,0.1); height: 24px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); position: relative; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); }
        .xp-bar-inner { height: 100%; background: linear-gradient(90deg, #d4af37, #fef3c7, #d4af37); background-size: 200% 100%; animation: shimmer 3s infinite linear; box-shadow: 0 0 15px rgba(212, 175, 55, 0.3); transition: width 0.8s cubic-bezier(0.17, 0.67, 0.83, 0.67); }
        .xp-bar-text { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-family: 'Cinzel', serif; font-size: 0.75rem; font-weight: bold; color: var(--xp-text-color); letter-spacing: 1.5px; z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        .xp-alert-glow { margin-top: 1rem; text-align: center; font-size: 0.85rem; color: var(--gold-color); animation: pulse 2s infinite; font-weight: bold; letter-spacing: 2px; font-family: 'Cinzel', serif; }
        .details-three-columns-grid { display: grid; grid-template-columns: 280px 1fr 300px; gap: 2rem; align-items: flex-start; }
        .magical-wheel-chart-integrated { width: 100%; height: 280px; display: flex; align-items: center; justify-content: center; position: relative; margin-bottom: 1.5rem; }
        .magical-wheel-svg { width: 100%; height: 100%; }
        .wheel-segment { transition: all 0.5s ease; cursor: default; }
        .wheel-segment.active { filter: drop-shadow(0 0 5px rgba(212, 175, 55, 0.3)); }
        .trophy-icon { filter: drop-shadow(0 0 10px rgba(0,0,0,0.2)); pointer-events: none; }
        .info-box-integrated { padding: 0; }
        .wheel-description-text { font-family: 'Spectral', serif; font-size: 0.95rem; color: var(--text-secondary); font-style: italic; margin: 0.5rem 0 0 0; line-height: 1.5; }
        .section-item-list { margin-bottom: 2.5rem; }
        .magical-header { font-family: 'Cinzel', serif; font-size: 1.1rem; color: var(--text-color); margin: 0; }
        .magical-separator-section { border: 0; height: 1px; background-image: linear-gradient(to right, var(--gold-color), transparent); margin: 0.4rem 0 1.2rem 0; }
        
        .items-vertical { display: flex; flex-direction: column; gap: 0.6rem; }
        .item-card-v3 { 
            background: var(--card-v3-bg); 
            border: 1px solid var(--border-color);
            border-radius: 0.8rem; padding: 0.7rem 1.2rem;
            transition: all 0.2s ease;
            position: relative; overflow: hidden;
        }
        .item-card-v3.clickable:hover { transform: translateX(5px); background: var(--card-v3-hover); }
        .item-card-v3.daily { border-left: 3px solid var(--gold-color); }
        .item-card-v3.penalty { border-left: 3px solid #741b1b; }
        .item-card-v3.quest { border-left: 3px solid #1a237e; }
        .item-card-v3.done { border-left: 3px solid #2d5a27; opacity: 0.6; }

        .card-v3-header-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .card-v3-title-group { display: flex; align-items: center; }
        .card-v3-name-minimal { font-family: 'Cinzel', serif; font-size: 0.85rem; font-weight: 700; color: var(--text-color); letter-spacing: 0.3px; }
        
        .card-v3-meta-minimal { display: flex; align-items: center; gap: 0.6rem; }
        .card-v3-xp-minimal { display: flex; align-items: center; gap: 0.4rem; font-family: 'Cinzel', serif; font-size: 0.65rem; font-weight: bold; letter-spacing: 0.5px; }
        .xp-gain-val { color: #2d5a27; }
        .xp-sep-val { opacity: 0.4; color: var(--text-color); }
        .xp-damage-val { color: #741b1b; }
        
        .card-v3-xp-minimal.danger { color: #741b1b; }
        .card-v3-xp-minimal.info { color: #1a237e; }
        
        .card-v3-timer-minimal { font-family: 'Cinzel', serif; font-size: 0.65rem; color: #741b1b; font-weight: bold; background: var(--timer-bg); padding: 0.1rem 0.3rem; border-radius: 0.3rem; }
        [data-theme="dark"] .card-v3-timer-minimal { color: var(--gold-color); }
        
        /* Red Badge for Done items */
        .danger-badge-glow { 
            font-family: 'Cinzel', serif; font-size: 0.65rem; font-weight: bold; 
            color: #fff; background: #741b1b; padding: 0.1rem 0.6rem; 
            border-radius: 2rem; box-shadow: 0 0 10px rgba(116, 27, 27, 0.3);
            letter-spacing: 0.5px;
        }
        [data-theme="dark"] .danger-badge-glow { background: #b91c1c; box-shadow: 0 0 10px rgba(185, 28, 28, 0.5); }

        .card-v3-desc-subtitle { font-family: 'Spectral', serif; font-size: 0.75rem; color: var(--text-secondary); margin: 0.1rem 0 0 0; font-style: italic; opacity: 0.7; line-height: 1.2; }

        .header-challenges-v2 { color: var(--accent-color); }
        .magical-separator-challenges { border: 0; height: 2px; background: linear-gradient(to right, transparent, var(--gold-color), transparent); margin: 0.6rem 0 1.5rem 0; opacity: 0.6; }
        .challenges-vertical-list { display: flex; flex-direction: column; gap: 1rem; }
        .challenge-card-v2 { background: var(--card-bg); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.8rem; display: flex; flex-direction: column; gap: 0.5rem; transition: 0.3s; }
        .challenge-card-v2.completed { border-color: #741b1b; background: rgba(116, 27, 27, 0.05); opacity: 0.9; box-shadow: inset 0 0 10px rgba(116, 27, 27, 0.1); }
        [data-theme="dark"] .challenge-card-v2.completed { border-color: var(--gold-color); background: rgba(212, 175, 55, 0.05); }
        .challenge-card-v2.completed .challenge-v2-level { color: #741b1b; }
        [data-theme="dark"] .challenge-card-v2.completed .challenge-v2-level { color: var(--gold-color); }
        .challenge-card-v2.unlocked { border-color: var(--gold-color); background: rgba(212,175,55,0.08); transform: scale(1.02); box-shadow: 0 0 15px rgba(212,175,55,0.2); }
        .challenge-card-v2.locked { opacity: 0.3; }
        .challenge-v2-header { display: flex; justify-content: space-between; align-items: center; }
        .challenge-v2-level { font-family: 'Cinzel', serif; font-size: 0.65rem; color: var(--gold-color); font-weight: bold; }
        .challenge-v2-name { font-weight: 700; font-size: 0.9rem; line-height: 1.2; }
        .challenge-v2-desc { font-family: 'Spectral', serif; font-size: 0.8rem; color: var(--text-secondary); margin: 0; line-height: 1.3; font-style: italic; opacity: 0.8; }
        
        .edit-input-title { font-family: 'Cinzel', serif; font-size: 2.2rem; background: var(--input-bg); border: 1px solid var(--gold-color); color: var(--text-color); width: 100%; border-radius: 0.5rem; padding: 0.2rem 0.5rem; outline: none; }
        .edit-textarea-integrated { font-family: 'Spectral', serif; font-size: 0.95rem; width: 100%; min-height: 100px; background: var(--input-bg); border: 1px solid var(--gold-color); color: var(--text-color); border-radius: 0.5rem; padding: 0.8rem; outline: none; }
        .danger-zone-footer { margin-top: 4rem; padding-bottom: 3rem; }
        .magical-separator { border: 0; height: 1px; background-image: linear-gradient(to right, transparent, var(--border-color), transparent); margin-bottom: 1.5rem; }
        .footer-actions { display: flex; justify-content: flex-end; gap: 1rem; }
        .btn-danger, .btn-warning { color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 0.5rem; cursor: pointer; font-weight: 700; font-size: 0.85rem; transition: 0.2s; }
        .btn-danger { background: #741b1b; }
        .btn-warning { background: #b18e3a; }
        @keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        @keyframes pulse { 0% { transform: scale(1); opacity: 0.8; } 50% { transform: scale(1.05); opacity: 1; text-shadow: 0 0 15px var(--gold-color); } 100% { transform: scale(1); opacity: 0.8; } }
        .clickable { cursor: pointer; }
        @media (max-width: 1200px) { .details-three-columns-grid { grid-template-columns: 1fr; } }
    </style>
</div>
