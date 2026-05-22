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

    <div class="challenge-manager-container integrated-page-container">
        <!-- Alert Toast -->
        @if (session()->has('message'))
            <div class="alert-toast-magical" x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition>
                <div class="toast-content">
                    <span class="toast-icon">✨</span>
                    <span class="toast-message">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="toast-close">✕</button>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="alert-toast-error" x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition>
                <div class="toast-content">
                    <span class="toast-icon">⚡</span>
                    <span class="toast-message">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="toast-close">✕</button>
            </div>
        @endif

        <!-- Header Section -->
        <div class="page-header-mystic">
            <div class="title-section">
                <h1 class="spell-title-main">Desafios de nível</h1>
                <div class="magical-gradient-divider"></div>
            </div>
            <div class="header-controls">
                <!-- Filter Section -->
                <div class="filter-section-mystic">
                    <select id="filter_wheel" wire:model.live="filter_wheel_id" class="filter-select">
                        <option value="">Todas as Rodas</option>
                        @foreach($wheels as $wheel)
                            <option value="{{ $wheel->id }}">{{ $wheel->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button wire:click="openModal" class="btn-magical-seal">
                    <div class="seal-glow"></div>
                    <div class="seal-inner">
                        <span class="seal-sparkle">✨</span>
                        <span class="seal-text">NOVO DESAFIO</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Grid de Cards de Desafios -->
        <div class="spells-grid-mystic">
        @forelse ($challenges as $challenge)
            <div class="spell-card-wrapper">
                <div class="spell-card-organic">
                    <div class="card-aura"></div>
                    
                    <!-- Imagem do Desafio -->
                    <div class="spell-visual-organic">
                        @if($challenge->image)
                            <img src="{{ asset('storage/' . $challenge->image->path) }}" alt="{{ $challenge->name }}" class="spell-image-organic">
                        @else
                            <div class="spell-image-placeholder-organic">
                                <span class="icon">🏆</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-content-organic">
                        <h3 class="spell-name-organic">{{ $challenge->name }}</h3>
                        
                        
                        
                        <div class="ink-underline-organic"></div>
                        
                        <p class="spell-action-organic">
                            {{ Illuminate\Support\Str::limit($challenge->description, 80) ?? 'Sem descrição.' }}
                        </p>
                        <p class="spell-action-organic" style="margin-top: 0.2rem; font-weight: bold; color: var(--gold-color);">
                            {{ $challenge->prize_name }}
                        </p>

                        <div class="spell-stats-organic">
                            <div class="stat-item">
                                <span class="stat-label">Nível</span>
                                <span class="stat-value">{{ $challenge->level }}</span>
                            </div>
                        </div>

                        <div class="wheel-association">
                            🎡 {{ $challenge->wheel?->name ?? 'Sem Roda' }}
                        </div>
                    </div>
                </div>
                <!-- Quick Edit Button -->
                <button wire:click="edit({{ $challenge->id }})" class="quick-edit-btn" title="Editar Desafio">✎</button>
                <!-- Quick Delete Button -->
                <button wire:click="delete({{ $challenge->id }})" wire:confirm="Tem certeza que deseja excluir este desafio?" class="quick-delete-btn" title="Excluir Desafio">🗑️</button>
            </div>
        @empty
            <div class="empty-state-mystic">
                <div class="empty-icon-parchment">📭</div>
                <p class="empty-msg">Nenhum desafio encontrado. O grimório aguarda seus Desafios.</p>
                <button wire:click="openModal" class="btn-magical-seal">CONJURAR DESAFIO</button>
            </div>
        @endforelse
        </div>

        <!-- Paginação -->
        @if($challenges->hasPages())
            <div class="pagination-container-mystic">
                {{ $challenges->links('livewire.mystic-pagination') }}
            </div>
        @endif

        <!-- Modal de Cadastro/Edição -->
        @if($showModal)
        @teleport('body')
        <div class="modal-backdrop-mystic full-screen-modal" wire:click="closeModal">
            <div class="modal-parchment-spell" wire:click.stop>
                <div class="parchment-texture"></div>
                <div class="modal-header-mystic">
                    <div class="header-content-wrapper">
                        <h2>{{ $isEditing ? 'Reescrever Desafio' : 'Conjurar Novo Desafio' }}</h2>
                    </div>
                    <button wire:click="closeModal" class="close-btn-mystic">✕</button>
                </div>
                <form wire:submit.prevent="save" class="modal-form-full">
                    <div class="modal-body-mystic scrollable-content">
                        
                        <div class="form-group-parchment">
                            <label for="wheel_id">Pertence à Roda</label>
                            <select id="wheel_id" wire:model="wheel_id" class="input-parchment">
                                <option value="">Sem Roda (Desassociar)</option>
                                @foreach($wheels as $wheel)
                                    <option value="{{ $wheel->id }}">{{ $wheel->name }}</option>
                                @endforeach
                            </select>
                            @error('wheel_id') <span class="error-magical">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group-parchment">
                            <label for="challenge_name">Nome do Desafio</label>
                            <input type="text" id="challenge_name" wire:model="name" class="input-parchment" placeholder="Ex: Enfrentar o Trasgo">
                            @error('name') <span class="error-magical">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group-parchment">
                            <label for="challenge_description">Descrição</label>
                            <textarea id="challenge_description" wire:model="description" class="input-parchment textarea-compact" placeholder="Descreva o que deve ser feito..."></textarea>
                            @error('description') <span class="error-magical">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group-parchment">
                            <label for="challenge_level">Nível de Dificuldade (1 a 10)</label>
                            <input type="number" id="challenge_level" wire:model="level" min="1" max="10" class="input-parchment" placeholder="1">
                            @error('level') <span class="error-magical">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group-parchment">
                            <label for="challenge_prize_name">Nome do Prêmio</label>
                            <input type="text" id="challenge_prize_name" wire:model="prize_name" class="input-parchment" placeholder="Ex: Poção Felix Felicis">
                            @error('prize_name') <span class="error-magical">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group-parchment">
                            <label for="challenge_prize_description">Descrição do Prêmio</label>
                            <textarea id="challenge_prize_description" wire:model="prize_description" class="input-parchment textarea-compact" placeholder="Descreva os efeitos do prêmio..."></textarea>
                            @error('prize_description') <span class="error-magical">{{ $message }}</span> @enderror
                        </div>

                        <!-- Upload de Imagem -->
                        <div class="form-group-parchment">
                            <label for="challenge_image">Imagem do Desafio</label>
                            <input type="file" id="challenge_image" wire:model="image" accept="image/*" class="input-parchment" style="border: none; padding: 0.5rem 0;">
                            <div wire:loading wire:target="image" class="loading-text">Carregando imagem...</div>
                            @error('image') <span class="error-magical">{{ $message }}</span> @enderror
                            
                            <!-- Preview -->
                            @if ($image)
                                <div class="image-preview mt-2">
                                    <img src="{{ $image->temporaryUrl() }}" style="max-width: 100px; border-radius: 0.5rem; border: 1px solid rgba(44, 24, 16, 0.1);">
                                </div>
                            @endif
                        </div>

                    </div>
                    
                    <div class="modal-footer-mystic compact-footer">
                        <button type="button" wire:click="closeModal" class="btn-cancel-mystic">CANCELAR</button>
                        <button type="submit" class="btn-magical-seal">
                            <div class="seal-glow"></div>
                            <div class="seal-inner">
                                <span class="seal-text">{{ $isEditing ? 'SALVAR ALTERAÇÕES' : 'CONJURAR DESAFIO' }}</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endteleport
        @endif
    </div>

    <style>
        :root {
            --gold-color: #d4af37;
            --ink-color: #2c1810;
            --parchment-light: rgba(252, 248, 237, 0.4);
            --parchment-glow: rgba(252, 248, 237, 0.9);
            --page-bg-final: var(--bg-color);
        }

        /* Damage Highlight Red */
        .damage-highlight {
            color: #dc2626 !important;
        }

        /* Mystic Parchment Modal Styles */
        .modal-form-full {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-height: 0;
            overflow: hidden;
        }

        .modal-backdrop-mystic.full-screen-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            padding: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-parchment-spell {
            background: #f4ece0;
            width: 90vw;
            max-width: 600px;
            max-height: 90vh;
            border-radius: 12px;
            position: relative;
            display: flex;
            flex-direction: column;
            animation: modalFadeIn 0.5s ease-out;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }

        .parchment-texture {
            position: absolute;
            inset: 0;
            background-image: url('https://www.transparenttextures.com/patterns/old-map.png');
            opacity: 0.15;
            pointer-events: none;
        }

        .modal-header-mystic {
            padding: 1.5rem 2rem;
            background: rgba(210, 180, 140, 0.1);
            border-bottom: 2px solid var(--gold-color);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        .header-content-wrapper { display: flex; align-items: center; gap: 1rem; }
        .modal-header-mystic h2 {
            font-family: 'Cinzel', serif;
            font-size: 1.8rem;
            color: var(--ink-color);
            margin: 0;
            letter-spacing: 3px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .close-btn-mystic {
            position: absolute;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--ink-color);
            opacity: 0.5;
            transition: 0.3s;
        }
        .close-btn-mystic:hover {
            opacity: 1;
            color: #8b0000;
        }

        .modal-body-mystic.scrollable-content {
            padding: 2rem 3rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .modal-footer-mystic.compact-footer {
            padding: 1.5rem 3rem;
            background: rgba(210, 180, 140, 0.1);
            border-top: 1px dashed rgba(0,0,0,0.1);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 2rem;
            position: relative;
            z-index: 10;
        }

        .btn-cancel-mystic {
            background: none;
            border: none;
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            letter-spacing: 2px;
            cursor: pointer;
            color: #8b0000;
            opacity: 0.6;
            transition: 0.3s;
        }
        .btn-cancel-mystic:hover {
            opacity: 1;
            transform: scale(1.05);
        }

        .form-group-parchment {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 15;
            text-align: left;
        }
        .form-group-parchment label {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            font-weight: bold;
            margin-bottom: 0.4rem;
            color: var(--ink-color);
            text-transform: uppercase;
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
            box-sizing: border-box;
        }
        .input-parchment:focus {
            outline: none;
            border-bottom-color: var(--gold-color);
        }
        
        select.input-parchment {
            border-radius: 0;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='%232c1810' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.2rem;
            padding-right: 2rem;
            cursor: pointer;
        }

        .textarea-compact {
            resize: none;
            min-height: 80px;
            line-height: 1.5;
        }

        .error-magical {
            color: #8b0000;
            font-size: 0.75rem;
            font-weight: bold;
            margin-top: 0.2rem;
            display: block;
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

        /* Ambient Magical Environment */
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
            flex-wrap: wrap;
            gap: 1rem;
        }
        .title-section {
            flex: 1;
        }
        .spell-title-main {
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
        .header-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
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

        /* Filter Section */
        .filter-section-mystic {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filter-select {
            background: transparent;
            border: 1px solid rgba(44, 24, 16, 0.2);
            border-radius: 0.6rem;
            padding: 0.6rem 0.8rem;
            font-family: 'Spectral', serif;
            font-size: 0.85rem;
            color: var(--ink-color);
            cursor: pointer;
            transition: 0.2s;
            box-shadow: none;
            opacity: 0.8;
        }
        .filter-select:hover {
            border-color: rgba(44, 24, 16, 0.3);
            opacity: 1;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--gold-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1);
            opacity: 1;
        }

        /* Alert Toast */
        .alert-toast-magical,
        .alert-toast-error {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            padding: 0.8rem 1.2rem;
            border-radius: 0.8rem;
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            z-index: 3000;
            animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: calc(100vw - 3rem);
            width: 350px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .alert-toast-magical {
            background: linear-gradient(135deg, #fcf8ed, #f5ecd5);
            border: 2px solid var(--gold-color);
            color: var(--ink-color);
        }

        .alert-toast-error {
            background: linear-gradient(135deg, #fcf8ed, #fee2e2);
            border: 2px solid #dc2626;
            color: var(--ink-color);
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex: 1;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--ink-color);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.2rem 0.4rem;
            opacity: 0.6;
            transition: opacity 0.2s;
            font-weight: bold;
            flex-shrink: 0;
        }

        .toast-close:hover {
            opacity: 1;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Compact Organic Cards */
        .spells-grid-mystic {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            position: relative;
            z-index: 5;
        }
        .spell-card-wrapper { position: relative; }
        .spell-card-link { text-decoration: none !important; color: inherit; }
        .spell-card-organic {
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
        .spell-card-organic:hover {
            background: var(--parchment-glow);
            border-color: var(--gold-color);
            transform: translateY(-8px) rotate(0.5deg);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .quick-edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--gold-color);
            border: none;
            color: white;
            cursor: pointer;
            opacity: 0;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .quick-delete-btn {
            position: absolute;
            top: 50px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ef4444;
            border: none;
            color: white;
            cursor: pointer;
            opacity: 0;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .spell-card-wrapper:hover .quick-edit-btn { opacity: 1; }
        .spell-card-wrapper:hover .quick-delete-btn { opacity: 1; }
        .quick-edit-btn:hover { transform: scale(1.1); background: #8b0000; }
        .quick-delete-btn:hover { transform: scale(1.1); background: #dc2626; }

        .card-aura {
            position: absolute;
            inset: -8px;
            border-radius: inherit;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s;
        }
        .spell-card-organic:hover .card-aura {
            opacity: 1;
        }

        .spell-visual-organic { margin-bottom: 1rem; width: 130px; height: 130px; }
        .spell-image-organic {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .spell-image-placeholder-organic {
            width: 100%;
            height: 100%;
            background: rgba(44, 24, 16, 0.05);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .card-content-organic { text-align: center; width: 100%; display: flex; flex-direction: column; flex-grow: 1; }
        .spell-name-organic { font-family: 'Cinzel', serif; font-size: 1.1rem; color: var(--ink-color); margin: 0; letter-spacing: 0.5px; }
        .spell-type-badge { font-family: 'Spectral', serif; font-size: 0.75rem; color: var(--gold-color); font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 1px; opacity: 0.8; }
        .neutral-type { color: #64748b; }
        .ink-underline-organic { height: 1px; width: 40%; background: var(--ink-color); margin: 6px auto 8px; opacity: 0.15; border-radius: 50%; }
        .spell-action-organic { font-family: 'Spectral', serif; font-size: 0.85rem; color: var(--ink-color); opacity: 0.65; font-style: italic; line-height: 1.3; margin-bottom: 1rem; }

        .spell-stats-organic { display: flex; justify-content: center; align-items: center; gap: 1.5rem; margin-bottom: 1.2rem; }
        .stat-item { display: flex; flex-direction: column; align-items: center; }
        .stat-label { font-family: 'Cinzel', serif; font-size: 0.6rem; opacity: 0.4; text-transform: uppercase; }
        .stat-value { font-family: 'Cinzel', serif; font-size: 0.95rem; font-weight: 900; color: var(--ink-color); }
        .stat-divider { width: 1px; height: 18px; background: rgba(0,0,0,0.08); }

        .wheel-association {
            font-family: 'Spectral', serif;
            font-size: 0.8rem;
            color: var(--ink-color);
            opacity: 0.7;
            margin-top: auto;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(44, 24, 16, 0.1);
            width: 100%;
        }

        .alert-magical {
            background: rgba(45, 90, 39, 0.08); border: 1px solid rgba(45, 90, 39, 0.2); color: #2d5a27; padding: 0.8rem 1.5rem; border-radius: 2rem; margin-bottom: 2rem; font-family: 'Cinzel', serif; font-size: 0.85rem; text-align: center; display: flex; align-items: center; justify-content: center; gap: 0.5rem; position: relative; z-index: 5;
        }

        .empty-state-mystic { text-align: center; padding: 4rem 2rem; grid-column: 1/-1; background: var(--parchment-light); border-radius: 2rem; border: 1px dashed rgba(44, 24, 16, 0.2); position: relative; z-index: 5; }
        .empty-icon-parchment { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .empty-msg { font-family: 'Cinzel', serif; color: var(--ink-color); opacity: 0.7; margin-bottom: 2rem; }

        /* Pagination */
        .pagination-container-mystic {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 5;
            width: 100%;
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(44, 24, 16, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.2s ease-out;
            padding: 1rem;
            box-sizing: border-box;
        }

        /* Modal de Confirmação */
        .modal-content-confirmation {
            background: var(--parchment-glow);
            width: 100%;
            max-width: 400px;
            border-radius: 1.5rem;
            border: 2px solid rgba(44, 24, 16, 0.15);
            box-shadow: 0 25px 50px rgba(44, 24, 16, 0.25);
            overflow: hidden;
            animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 2rem;
            text-align: center;
        }

        .confirmation-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .confirmation-title {
            font-family: 'Cinzel', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ink-color);
            margin: 0 0 0.5rem 0;
            letter-spacing: 0.5px;
        }

        .confirmation-message {
            font-family: 'Spectral', serif;
            font-size: 0.9rem;
            color: var(--ink-color);
            opacity: 0.75;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .confirmation-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-cancel {
            background: rgba(44, 24, 16, 0.1);
            border: 1px solid rgba(44, 24, 16, 0.15);
            color: var(--ink-color);
            padding: 0.7rem 1.5rem;
            border-radius: 0.8rem;
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-cancel:hover {
            background: rgba(44, 24, 16, 0.15);
            border-color: rgba(44, 24, 16, 0.3);
        }

        .btn-confirm-delete {
            background: #dc2626;
            border: none;
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 0.8rem;
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }

        .btn-confirm-delete:hover {
            background: #b91c1c;
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
            transform: translateY(-1px);
        }

        .challenge-manager-container {
            width: 100%;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 0.4; }
        }
    </style>
</div>
