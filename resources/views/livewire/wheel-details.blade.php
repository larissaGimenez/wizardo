<div class="wheel-details-container full-width" x-data="{ 
    introActive: false,
    phrase: '',
    fullPhrase: '“Eu juro solenemente não fazer nada de bom. . .”',
    playMaraudersReveal() {
        this.introActive = true;
        this.phrase = '';
        
        /* Play the real audio file from public/audio/hptheme.m4a */
        const audio = new Audio('{{ asset('audio/hptheme.m4a') }}');
        audio.volume = 0.7;
        audio.play().catch(e => console.error('Audio play failed:', e));

        /* Handwriting Logic */
        let i = 0;
        let interval = setInterval(() => {
            if (i < this.fullPhrase.length) {
                this.phrase += this.fullPhrase[i];
                i++;
            } else {
                clearInterval(interval);
                /* Reveal the tracker shortly after the phrase is complete */
                setTimeout(() => {
                    $wire.toggleTracker().then(() => {
                        /* Keep overlay briefly to cover the modal animation start */
                        setTimeout(() => { this.introActive = false; }, 800);
                    });
                }, 1000);
            }
        }, 110);
    }
}">
    <!-- Intro Overlay (Marauder's Oath) -->
    <div class="marauders-intro-overlay" 
         x-show="introActive" 
         x-transition:enter="intro-fade-in" 
         x-transition:leave="intro-fade-out"
         style="display: none;">
        <div class="oath-container">
            <h2 class="oath-text" x-text="phrase"></h2>
            <div class="ink-stain-effect"></div>
        </div>
    </div>

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

    <div class="header-breadcrumb-row">
        <div class="breadcrumb">
            <nav class="magical-breadcrumb">
                <a href="{{ route('wheel.manager') }}">Rodas</a>
                <span class="magical-arrow">›</span>
                <span class="current">Detalhes da Roda</span>
            </nav>
        </div>
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
                
                <div class="info-box-integrated centered">
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

                    <button @click="playMaraudersReveal()" class="btn-tracker-trigger-centered">
                        📜 Mapa do Maroto
                    </button>
                </div>
            </div>
        </div>

        <!-- Coluna 2: Feitiços e Missões -->
        <div class="column column-actions">
            <!-- Feitiços Diários -->
            <div class="section-item-list">
                <div class="magical-header-summary">
                    <h2 class="magical-header">Feitiços Diários</h2>
                    @if($dailySpellsXp > 0)
                        <span class="daily-summary-badge success">+{{ $dailySpellsXp }} XP HOJE</span>
                    @endif
                </div>
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
                                        <span class="danger-badge-glow">Malfeito feito!</span>
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
                <div class="magical-header-summary">
                    <h2 class="magical-header">Penalidades das Trevas</h2>
                    @if($penaltiesCount > 0)
                        <span class="daily-summary-badge danger">{{ $penaltiesCount }} SOFRIDAS | {{ $penaltiesDamage }} XP</span>
                    @endif
                </div>
                <hr class="magical-separator-section">
                <div class="items-vertical">
                    @forelse($spells->where('type', 'penalidade das trevas') as $spell)
                        @php $countToday = $itemCounts['App\Models\Spell:' . $spell->id] ?? 0; @endphp
                        <div class="item-card-v3 penalty clickable" wire:click="useSpell({{ $spell->id }})">
                            <div class="card-v3-header-row">
                                <div class="card-v3-title-group">
                                    <span class="card-v3-name-minimal">{{ $spell->name }}</span>
                                    @if($countToday > 0)
                                        <span class="card-v3-item-count danger">{{ $countToday }}x</span>
                                    @endif
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
                <div class="magical-header-summary">
                    <h2 class="magical-header">Missões de Hogsmead</h2>
                    @if($questsCount > 0)
                        <span class="daily-summary-badge info">{{ $questsCount }} FEITAS | +{{ $questsGain }} XP</span>
                    @endif
                </div>
                <hr class="magical-separator-section">
                <div class="items-vertical">
                    @forelse($quests as $quest)
                        @php $countToday = $itemCounts['App\Models\Quest:' . $quest->id] ?? 0; @endphp
                        <div class="item-card-v3 quest clickable" wire:click="completeQuest({{ $quest->id }})">
                            <div class="card-v3-header-row">
                                <div class="card-v3-title-group">
                                    <span class="card-v3-name-minimal">{{ $quest->name }}</span>
                                    @if($countToday > 0)
                                        <span class="card-v3-item-count info">{{ $countToday }}x</span>
                                    @endif
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
                         @if($canTry) wire:click="completeChallenge({{ $challenge->id }})" style="pointer-events: auto; cursor: pointer;" @endif>
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

    <!-- Habit Tracker Modal (Mapa do Maroto) -->
    @if($showTracker)
    <div class="tracker-overlay" wire:click.self="toggleTracker">
        <div class="tracker-modal marauders-map-reveal">
            <div class="tracker-header">
                <div class="tracker-nav">
                    <button wire:click="previousMonth" class="tracker-nav-btn">‹</button>
                    <h2 class="tracker-title">{{ $trackerDateTitle }}</h2>
                    <button wire:click="nextMonth" class="tracker-nav-btn">›</button>
                </div>
                <button wire:click="toggleTracker" class="tracker-close">&times;</button>
            </div>

            <div class="tracker-body-compact">
                <div class="tracker-table-container">
                    <table class="tracker-compact-table">
                        <thead>
                            <tr>
                                <th class="label-col">Atividade</th>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    <th class="day-col">{{ $d }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Seção Feitiços -->
                            <tr class="section-row"><td colspan="{{ $daysInMonth + 1 }}">Feitiços Diários</td></tr>
                            @foreach($trackerSpells->where('type', 'feitiço diário') as $spell)
                            <tr>
                                <td class="label-col">{{ $spell->name }}</td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php 
                                        $hasDone = isset($trackerCompletions['App\Models\Spell_' . $spell->id . '_' . $d]);
                                        $isToday = $trackerMonth == now()->month && $trackerYear == now()->year && $d == now()->day;
                                    @endphp
                                    <td class="cell-col {{ $hasDone ? 'done-spell' : '' }} {{ $isToday ? 'is-today' : '' }}">
                                        @if($hasDone) <div class="mark">✨</div> @endif
                                    </td>
                                @endfor
                            </tr>
                            @endforeach

                            <!-- Seção Penalidades -->
                            <tr class="section-row"><td colspan="{{ $daysInMonth + 1 }}">Penalidades das Trevas</td></tr>
                            @foreach($trackerSpells->where('type', 'penalidade das trevas') as $spell)
                            <tr>
                                <td class="label-col">{{ $spell->name }}</td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php 
                                        $actions = $trackerCompletions['App\Models\Spell_' . $spell->id . '_' . $d] ?? null;
                                        $count = $actions ? count($actions) : 0;
                                        $isToday = $trackerMonth == now()->month && $trackerYear == now()->year && $d == now()->day;
                                    @endphp
                                    <td class="cell-col {{ $count > 0 ? 'done-penalty' : '' }} {{ $isToday ? 'is-today' : '' }}">
                                        @if($count > 0) <span class="count-text">{{ $count }}</span> @endif
                                    </td>
                                @endfor
                            </tr>
                            @endforeach

                            <!-- Seção Missões -->
                            <tr class="section-row"><td colspan="{{ $daysInMonth + 1 }}">Missões de Hogsmead</td></tr>
                            @foreach($trackerQuests as $quest)
                            <tr>
                                <td class="label-col">{{ $quest->name }}</td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php 
                                        $actions = $trackerCompletions['App\Models\Quest_' . $quest->id . '_' . $d] ?? null;
                                        $count = $actions ? count($actions) : 0;
                                        $isToday = $trackerMonth == now()->month && $trackerYear == now()->year && $d == now()->day;
                                    @endphp
                                    <td class="cell-col {{ $count > 0 ? 'done-quest' : '' }} {{ $isToday ? 'is-today' : '' }}">
                                        @if($count > 0) <span class="count-text">{{ $count }}</span> @endif
                                    </td>
                                @endfor
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="tracker-footer-compact">
                <div class="legend-compact">
                    <span class="legend-item"><span class="box-c spell"></span> Feitiço</span>
                    <span class="legend-item"><span class="box-c penalty"></span> Penalidade</span>
                    <span class="legend-item"><span class="box-c quest"></span> Missão</span>
                </div>
            </div>
        </div>
    </div>
    @endif

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

    <!-- Ambient Magical Dust (Randomly positioned) -->
    <div class="magical-dust-overlay" style="position: absolute; inset: 0; pointer-events: none; overflow: hidden;">
        @for($m = 0; $m < 15; $m++)
            <div class="magical-dust" style="--x: {{ rand(0, 100) }}vw; --x2: {{ rand(0, 100) }}vw; --d: {{ rand(10, 25) }}s; left: 0; top: 0;"></div>
        @endfor
    </div>

    <style>
        :root {
            --segment-inactive: rgba(0, 0, 0, 0.03);
            --segment-stroke: rgba(0, 0, 0, 0.05);
            --card-v3-bg: rgba(255, 255, 255, 0.5);
            --card-v3-hover: rgba(255, 255, 255, 0.8);
            --timer-bg: rgba(116, 27, 27, 0.04);
            --xp-text-color: #1a1a1a;
            --danger-badge: #741b1b;
            
            /* Tints from Tracker */
            --bg-daily: rgba(212, 175, 55, 0.1);
            --bg-penalty: rgba(116, 27, 27, 0.08);
            --bg-quest: rgba(26, 35, 126, 0.08);
            
            --page-bg-final: var(--card-bg); /* Uniform lighter parchment */
        }

        [data-theme="dark"] {
            --segment-inactive: rgba(255, 255, 255, 0.03);
            --segment-stroke: rgba(255, 255, 255, 0.05);
            --card-v3-bg: rgba(255, 255, 255, 0.03);
            --card-v3-hover: rgba(255, 255, 255, 0.07);
            --timer-bg: rgba(212, 175, 55, 0.1);
            --xp-text-color: #fef3c7;
            --danger-badge: #d4af37;

            --bg-daily: rgba(212, 175, 55, 0.15);
            --bg-penalty: rgba(116, 27, 27, 0.15);
            --bg-quest: rgba(96, 165, 250, 0.12);

            --page-bg-final: var(--sidebar-bg); /* Unified dark background */
        }

        .wheel-details-container.full-width { 
            max-width: 100% !important; 
            margin: -2rem !important; 
            padding: 2rem !important; 
            background: var(--page-bg-final) !important;
            min-height: calc(100vh - 60px);
            position: relative;
            overflow: hidden; /* For floating motes */
            animation: pageEntryReveal 1.2s ease-out;
        }

        @keyframes pageEntryReveal {
            from { opacity: 0; transform: translateY(10px); filter: blur(5px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        /* Dust Motes / Magical Sparks */
        .magical-dust {
            position: absolute;
            width: 4px; height: 4px;
            background: var(--gold-color);
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.15;
            filter: blur(1px);
            animation: floatMote var(--d) infinite linear;
            z-index: 0;
        }
        @keyframes floatMote {
            0% { transform: translate(var(--x), 110vh) scale(0); opacity: 0; }
            50% { opacity: 0.2; }
            100% { transform: translate(var(--x2), -10vh) scale(1.5); opacity: 0; }
        }

        .marauders-intro-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 1); z-index: 5000;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(15px);
        }
        .oath-container { position: relative; text-align: center; }
        .oath-text { 
            font-family: 'Cinzel', serif; font-size: 2.5rem; color: var(--gold-color);
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.8);
            max-width: 800px; line-height: 1.4;
            min-height: 4em;
        }
        .ink-stain-effect {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 250px; height: 250px; background: radial-gradient(circle, rgba(116, 27, 27, 0.3) 0%, transparent 75%);
            z-index: -1; animation: inkExpand 3s infinite alternate;
        }
        @keyframes inkExpand { 0% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; } 100% { transform: translate(-50%, -50%) scale(1.8); opacity: 0.7; } }

        .intro-fade-in { animation: fadeIn 0.3s ease-out; }
        .intro-fade-out { animation: fadeOut 0.8s ease-in forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        .header-breadcrumb-row::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 120px; 
            background: transparent; /* Removed overlay to keep background uniform */
            pointer-events: none;
            z-index: 0;
        }

        .header-breadcrumb-row, .details-header-main { position: relative; z-index: 1; }
        .header-breadcrumb-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        
        .btn-tracker-trigger-centered { 
            margin-top: 1.5rem;
            background: var(--accent-color); color: white; border: 1px solid var(--gold-color); 
            padding: 0.6rem 1.5rem; border-radius: 2rem; font-family: 'Cinzel', serif; 
            font-size: 0.8rem; cursor: pointer; transition: all 0.3s; 
            box-shadow: 0 4px 15px rgba(116, 27, 27, 0.2);
            text-transform: uppercase; letter-spacing: 1.5px;
        }
        .btn-tracker-trigger-centered:hover { 
            transform: translateY(-3px) scale(1.05); 
            box-shadow: 0 8px 25px rgba(116, 27, 27, 0.4); 
            background: var(--accent-hover); 
            letter-spacing: 2px;
        }

        .toast { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 3000; display: flex; align-items: center; gap: 0.8rem; padding: 0.8rem 1.2rem; border-radius: 0.8rem; border: 2px solid var(--gold-color); background: var(--card-bg); font-size: 0.9rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .magical-breadcrumb { font-family: 'Cinzel', serif; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem; }
        .magical-breadcrumb a { color: var(--accent-color); text-decoration: none; }
        .magical-arrow { color: var(--accent-color); font-weight: bold; font-size: 1.1rem; opacity: 0.6; }
        .details-header-main { margin-bottom: 1.5rem; }
        .header-flex-wrapper { display: flex; align-items: center; gap: 1.5rem; }
        .level-badge-pill { background: var(--gold-color); color: #000; display: flex; align-items: center; border-radius: 2rem; overflow: hidden; font-family: 'Cinzel', serif; font-weight: bold; box-shadow: 0 0 15px rgba(212, 175, 55, 0.3); }
        .level-num { padding: 0.4rem 1rem; background: rgba(0,0,0,0.1); font-size: 1rem; }
        .level-name { padding: 0.4rem 1rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .wheel-title { 
            font-family: 'Cinzel', serif; 
            font-size: 2.2rem; 
            margin: 0; 
            color: var(--text-color); 
            line-height: 1; 
            letter-spacing: 1px;
            animation: titlePulse 4s infinite ease-in-out;
        }
        @keyframes titlePulse {
            0%, 100% { text-shadow: 0 0 5px transparent; }
            50% { text-shadow: 0 0 15px rgba(212, 175, 55, 0.4); }
        }
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
        
        .info-box-integrated.centered { display: flex; flex-direction: column; align-items: center; text-align: center; }
        .section-sub-title { font-family: 'Cinzel', serif; font-size: 1.1rem; color: var(--text-color); margin-bottom: 0.5rem; width: 100%; }
        .wheel-description-text { font-family: 'Spectral', serif; font-size: 0.95rem; color: var(--text-secondary); font-style: italic; margin: 0.5rem 0 0 0; line-height: 1.5; width: 100%; max-width: 250px; }
        
        .section-item-list { margin-bottom: 2.5rem; }
        .magical-header-summary { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.2rem; }
        .magical-header { font-family: 'Cinzel', serif; font-size: 1.1rem; color: var(--text-color); margin: 0; }
        .daily-summary-badge { font-family: 'Cinzel', serif; font-size: 0.65rem; font-weight: bold; opacity: 0.8; letter-spacing: 0.5px; }
        .daily-summary-badge.success { color: #2d5a27; }
        .daily-summary-badge.danger { color: #741b1b; }
        .daily-summary-badge.info { color: #1a237e; }
        [data-theme="dark"] .daily-summary-badge.success { color: #4ade80; }
        [data-theme="dark"] .daily-summary-badge.danger { color: #f87171; }
        [data-theme="dark"] .daily-summary-badge.info { color: #60a5fa; }
        .magical-separator-section { border: 0; height: 1px; background-image: linear-gradient(to right, var(--gold-color), transparent); margin: 0.2rem 0 1rem 0; }
        .items-vertical { display: flex; flex-direction: column; gap: 0.6rem; }
        .item-card-v3 { background: var(--card-v3-bg); border: 1px solid var(--border-color); border-radius: 0.8rem; padding: 0.7rem 1.2rem; transition: all 0.2s ease; position: relative; overflow: hidden; }
        .item-card-v3.daily { border-left: 3px solid var(--gold-color); background: var(--bg-daily); }
        .item-card-v3.penalty { border-left: 3px solid #741b1b; background: var(--bg-penalty); }
        .item-card-v3.quest { border-left: 3px solid #1a237e; background: var(--bg-quest); }
        .item-card-v3.done { border-left: 3px solid #2d5a27; opacity: 0.6; background: rgba(0,0,0,0.05); }
        .item-card-v3.clickable:hover { 
            transform: translateX(5px) scale(1.02); 
            filter: brightness(1.1); 
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2), 0 5px 15px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .item-card-v3.penalty.clickable:hover { 
            box-shadow: 0 0 20px rgba(116, 27, 27, 0.2), 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-v3-header-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .card-v3-title-group { display: flex; align-items: center; gap: 0.6rem; }
        .card-v3-name-minimal { font-family: 'Cinzel', serif; font-size: 0.85rem; font-weight: 700; color: var(--text-color); letter-spacing: 0.3px; }
        .card-v3-item-count { font-family: 'Cinzel', serif; font-size: 0.65rem; font-weight: bold; padding: 0.1rem 0.4rem; border-radius: 0.3rem; }
        .card-v3-item-count.danger { background: rgba(116, 27, 27, 0.08); color: #741b1b; }
        .card-v3-item-count.info { background: rgba(26, 35, 126, 0.08); color: #1a237e; }
        [data-theme="dark"] .card-v3-item-count.danger { background: rgba(185, 28, 28, 0.15); color: #f87171; }
        [data-theme="dark"] .card-v3-item-count.info { background: rgba(96, 165, 250, 0.15); color: #60a5fa; }
        .card-v3-meta-minimal { display: flex; align-items: center; gap: 0.6rem; }
        .card-v3-xp-minimal { display: flex; align-items: center; gap: 0.4rem; font-family: 'Cinzel', serif; font-size: 0.65rem; font-weight: bold; letter-spacing: 0.5px; }
        .xp-gain-val { color: #2d5a27; }
        .xp-sep-val { opacity: 0.4; color: var(--text-color); }
        .xp-damage-val { color: #741b1b; }
        .card-v3-timer-minimal { font-family: 'Cinzel', serif; font-size: 0.65rem; color: #741b1b; font-weight: bold; background: var(--timer-bg); padding: 0.1rem 0.3rem; border-radius: 0.3rem; }
        [data-theme="dark"] .card-v3-timer-minimal { color: var(--gold-color); }
        .danger-badge-glow { font-family: 'Cinzel', serif; font-size: 0.65rem; font-weight: bold; color: #fff; background: #741b1b; padding: 0.1rem 0.6rem; border-radius: 2rem; box-shadow: 0 0 10px rgba(116, 27, 27, 0.3); letter-spacing: 0.5px; }
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

        .marauders-map-reveal {
            animation: maraudersReveal 1.2s cubic-bezier(0.19, 1, 0.22, 1) forwards;
            transform-origin: center;
        }

        @keyframes maraudersReveal {
            0% { 
                opacity: 0; 
                clip-path: circle(0% at 50% 50%); 
                filter: blur(10px) sepia(1) brightness(0.5);
                transform: scale(0.8) translateY(20px);
            }
            40% {
                opacity: 0.6;
                clip-path: circle(30% at 50% 50%);
                filter: blur(5px) sepia(0.5) brightness(0.8);
            }
            100% { 
                opacity: 1; 
                clip-path: circle(150% at 50% 50%); 
                filter: blur(0px) sepia(0) brightness(1);
                transform: scale(1) translateY(0);
            }
        }

        .tracker-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 4000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(15px); }
        .tracker-modal { background: var(--card-bg); width: 98%; max-width: 1400px; height: 90vh; border-radius: 1.5rem; border: 3px solid var(--gold-color); display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 0 60px rgba(0,0,0,0.6); }
        .tracker-header { padding: 1rem 2rem; border-bottom: 2px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.03); }
        .tracker-nav { display: flex; align-items: center; gap: 2rem; }
        .tracker-nav-btn { background: none; border: 1px solid var(--border-color); color: var(--text-color); font-size: 1.2rem; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
        .tracker-nav-btn:hover { background: var(--gold-color); color: black; border-color: var(--gold-color); }
        .tracker-title { font-family: 'Cinzel', serif; font-size: 1.5rem; margin: 0; color: var(--text-color); letter-spacing: 1px; }
        .tracker-close { background: none; border: none; font-size: 2rem; color: var(--text-color); cursor: pointer; opacity: 0.5; transition: 0.2s; }
        .tracker-close:hover { opacity: 1; color: var(--accent-color); }
        .tracker-body-compact { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 1rem 2rem; }
        .tracker-table-container { width: 100%; }
        .tracker-compact-table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .tracker-compact-table th, .tracker-compact-table td { border: 1px solid var(--border-color); font-family: 'Cinzel', serif; height: 30px; text-align: center; }
        .label-col { width: 180px; text-align: left !important; padding-left: 0.8rem !important; font-size: 0.65rem; font-weight: bold; background: rgba(0,0,0,0.02); }
        .day-col { width: calc((100% - 180px) / 31); font-size: 0.6rem; background: rgba(0,0,0,0.05); }
        .cell-col { font-size: 0.6rem; position: relative; background: rgba(255,255,255,0.01); }
        .section-row { background: rgba(116, 27, 27, 0.05); }
        .section-row td { font-family: 'Cinzel', serif; font-size: 0.75rem; font-weight: 900; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; padding: 0.4rem 1rem; text-align: left !important; }
        [data-theme="dark"] .section-row { background: rgba(212, 175, 55, 0.05); }
        [data-theme="dark"] .section-row td { color: var(--gold-color); }
        .done-spell { background: rgba(212, 175, 55, 0.12); }
        .done-penalty { background: rgba(116, 27, 27, 0.12); }
        .done-quest { background: rgba(26, 35, 126, 0.12); }
        .cell-col.is-today { outline: 1px solid var(--accent-color); outline-offset: -1px; background: rgba(116, 27, 27, 0.05); }
        .mark { font-size: 0.8rem; }
        .count-text { font-weight: bold; opacity: 0.8; }
        .tracker-footer-compact { padding: 0.8rem 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; }
        .legend-compact { display: flex; gap: 1.5rem; }
        .legend-item { display: flex; align-items: center; gap: 0.4rem; font-family: 'Spectral', serif; font-size: 0.8rem; }
        .box-c { width: 12px; height: 12px; border: 1px solid var(--border-color); border-radius: 2px; }
        .box-c.spell { background: rgba(212, 175, 55, 0.3); }
        .box-c.penalty { background: rgba(116, 27, 27, 0.3); }
        .box-c.quest { background: rgba(26, 35, 126, 0.3); }

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
