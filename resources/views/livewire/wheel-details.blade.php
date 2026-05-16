<div class="wheel-details-container full-width">
    <!-- Flash Message com Auto-close -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             class="alert alert-success toast">
            <span>✨ {{ session('message') }}</span>
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

    <div class="details-header-main">
        <div class="name-edit-container">
            @if($isEditingName)
                <div class="inline-edit">
                    <input type="text" 
                           wire:model="newName" 
                           wire:keydown.enter="confirmSave('name')" 
                           wire:keydown.escape="cancelEditing" 
                           wire:blur="confirmSave('name')"
                           autofocus 
                           class="edit-input-title">
                </div>
            @else
                <h1 class="wheel-title clickable" wire:click="startEditingName">
                    {{ $wheel->name }}
                </h1>
            @endif

            <div class="description-subtitle-container">
                @if($isEditingDescription)
                    <div class="inline-edit">
                        <textarea wire:model="newDescription" 
                                  wire:keydown.enter="confirmSave('description')" 
                                  wire:keydown.escape="cancelEditing" 
                                  wire:blur="confirmSave('description')"
                                  autofocus 
                                  class="edit-textarea-subtitle"></textarea>
                    </div>
                @else
                    <p class="wheel-description-subtitle clickable" wire:click="startEditingDescription">
                        {{ $wheel->description ?? 'Clique para adicionar uma descrição...' }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="details-content-grid">
        <div class="details-sidebar-card">
            <div class="wheel-visual-large">
                @if($wheel->image)
                    <img src="{{ asset('storage/' . $wheel->image->path) }}" alt="{{ $wheel->name }}">
                @else
                    <div class="placeholder">🎡</div>
                @endif
            </div>
            
            <div class="wheel-info-box">
                <h3 class="section-sub-title">Desafios de Nível</h3>
                <div class="items-list-sidebar">
                    @forelse($challenges as $challenge)
                        <div class="item-pill-mini challenge">
                            <span class="item-name">{{ $challenge->name }}</span>
                            <span class="item-level">Nv {{ $challenge->level }}</span>
                        </div>
                    @empty
                        <p class="empty-text">Nenhum desafio associado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="details-main-lists">
            <!-- Feitiços Diários -->
            <div class="section">
                <h2 class="magical-header">Feitiços Diários</h2>
                <hr class="magical-separator-section">
                <div class="items-list">
                    @forelse($spells->where('type', 'feitiço diário') as $spell)
                        <div class="item-pill-small">
                            <span class="item-name">{{ $spell->name }}</span>
                        </div>
                    @empty
                        <p class="empty-text">Nenhum feitiço diário.</p>
                    @endforelse
                </div>
            </div>

            <!-- Penalidades das Trevas -->
            <div class="section">
                <h2 class="magical-header">Penalidades das Trevas</h2>
                <hr class="magical-separator-section">
                <div class="items-list">
                    @forelse($spells->where('type', 'penalidade das trevas') as $spell)
                        <div class="item-pill-small danger">
                            <span class="item-name">{{ $spell->name }}</span>
                        </div>
                    @empty
                        <p class="empty-text">Nenhuma penalidade.</p>
                    @endforelse
                </div>
            </div>

            <!-- Missões de Hogsmead -->
            <div class="section">
                <h2 class="magical-header">Missões de Hogsmead</h2>
                <hr class="magical-separator-section">
                <div class="items-list">
                    @forelse($quests as $quest)
                        <div class="item-pill-small success">
                            <span class="item-name">{{ $quest->name }}</span>
                        </div>
                    @empty
                        <p class="empty-text">Nenhuma missão.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé de Ações Perigosas -->
    <div class="danger-zone-footer">
        <hr class="magical-separator">
        <div class="footer-actions">
            <button wire:click="confirmDelete" class="btn btn-danger btn-sm">
                🗑️ Banir esta Roda
            </button>
        </div>
    </div>

    <!-- Modais -->
    @if($showConfirmModal)
        <div class="modal-backdrop" wire:click="cancelEditing">
            <div class="modal-content confirm-modal" wire:click.stop>
                <div class="modal-header-magic">
                    <h2>✨ Selar Alteração?</h2>
                </div>
                <div class="modal-body text-center">
                    <p>Deseja confirmar as mudanças?</p>
                    <div class="modal-magic-actions">
                        <button wire:click="save" class="btn-magic-primary">Confirmar</button>
                        <button wire:click="cancelEditing" class="btn-magic-secondary">Descartar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="modal-backdrop" wire:click="cancelEditing">
            <div class="modal-content confirm-modal" wire:click.stop>
                <div class="modal-header-magic danger">
                    <h2>🔥 Banir Roda?</h2>
                </div>
                <div class="modal-body text-center">
                    <p>Tem certeza que deseja excluir permanentemente a roda <strong>"{{ $wheel->name }}"</strong>?</p>
                    <div class="modal-magic-actions">
                        <button wire:click="delete" class="btn-magic-danger">Banir Roda</button>
                        <button wire:click="cancelEditing" class="btn-magic-secondary">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .wheel-details-container.full-width {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .toast {
            position: fixed; top: 1.5rem; right: 1.5rem; z-index: 3000;
            display: flex; align-items: center; gap: 0.8rem; padding: 0.8rem 1.2rem;
            border-radius: 0.8rem; border: 2px solid var(--gold-color);
            background: var(--card-bg); font-size: 0.9rem;
        }

        .close-alert { background: none; border: none; font-size: 1rem; cursor: pointer; opacity: 0.5; }

        /* Breadcrumb */
        .magical-breadcrumb {
            font-family: 'Cinzel', serif; font-size: 0.85rem;
            display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem;
        }
        .magical-breadcrumb a { color: var(--accent-color); text-decoration: none; }
        .magical-arrow { color: #741b1b; font-weight: bold; font-size: 1.1rem; }
        .magical-breadcrumb .current { opacity: 0.5; }

        .details-header-main { margin-bottom: 2rem; }

        .wheel-title {
            font-family: 'Cinzel', serif; font-size: 2.2rem; margin: 0;
            color: var(--text-color); line-height: 1.1;
        }

        .wheel-description-subtitle {
            font-family: 'Spectral', serif; font-size: 1rem;
            color: var(--text-secondary); opacity: 0.8; margin: 0.2rem 0 0 0.5rem;
            line-height: 1.4; font-style: italic;
        }

        .edit-input-title {
            font-family: 'Cinzel', serif; font-size: 2.2rem;
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--gold-color);
            color: var(--text-color); width: 100%; border-radius: 0.4rem;
            padding: 0.1rem 0.3rem; outline: none;
        }

        .edit-textarea-subtitle {
            font-family: 'Spectral', serif; font-size: 1rem;
            width: 100%; min-height: 60px; background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--gold-color); color: var(--text-color);
            border-radius: 0.4rem; padding: 0.3rem; outline: none; margin-top: 0.5rem;
        }

        .clickable { cursor: pointer; transition: background 0.2s; border-radius: 0.4rem; }
        .clickable:hover { background: rgba(196, 180, 148, 0.1); }

        .details-content-grid {
            display: grid; grid-template-columns: 320px 1fr; gap: 2rem;
        }

        .details-sidebar-card {
            background: var(--card-bg); border: 1px solid var(--border-color);
            border-radius: 1.2rem; overflow: hidden;
        }

        .wheel-visual-large { width: 100%; height: 240px; background: #000; display: flex; align-items: center; justify-content: center; border-bottom: 2px solid var(--gold-color); }
        .wheel-visual-large img { width: 100%; height: 100%; object-fit: cover; }

        .wheel-info-box { padding: 1.5rem; }

        .section-sub-title {
            font-family: 'Cinzel', serif; color: var(--accent-color);
            margin-bottom: 0.8rem; font-size: 1.1rem; border-bottom: 1px solid var(--border-color);
            display: inline-block;
        }

        .items-list-sidebar { display: flex; flex-direction: column; gap: 0.6rem; }

        .item-pill-mini {
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-color);
            padding: 0.4rem 0.8rem; border-radius: 0.6rem; display: flex;
            justify-content: space-between; align-items: center; font-size: 0.9rem;
        }
        .item-pill-mini.challenge { border-color: var(--gold-color); background: rgba(212, 175, 55, 0.05); }

        .section { margin-bottom: 2rem; }

        .magical-header { font-family: 'Cinzel', serif; font-size: 1.25rem; color: var(--text-color); }

        .magical-separator-section {
            border: 0; height: 1px;
            background-image: linear-gradient(to right, var(--gold-color), var(--border-color), transparent);
            margin: 0.5rem 0 1.2rem 0;
        }

        .items-list { display: flex; flex-wrap: wrap; gap: 0.6rem; }

        .item-pill-small {
            background: var(--card-bg); border: 1px solid var(--border-color);
            padding: 0.4rem 1rem; border-radius: 1.5rem; font-size: 0.9rem;
            font-family: 'Spectral', serif;
        }

        .item-pill-small.danger { border-color: #741b1b; background: rgba(116, 27, 27, 0.03); }
        .item-pill-small.success { border-color: #2d5a27; background: rgba(45, 90, 39, 0.03); }

        .item-name { font-weight: 600; }
        .item-level { font-size: 0.75rem; opacity: 0.7; font-weight: 800; }

        .empty-text { opacity: 0.5; font-size: 0.85rem; font-style: italic; }

        /* Modal Styles */
        .modal-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center; z-index: 2000;
        }
        .modal-content {
            background: var(--modal-bg); border: 2px solid var(--gold-color);
            border-radius: 1.2rem; padding: 1.5rem; width: 90%; max-width: 400px; font-size: 0.95rem;
        }
        .modal-header-magic { text-align: center; padding-bottom: 1rem; border-bottom: 1px solid var(--gold-color); margin-bottom: 1rem; }
        .modal-header-magic h2 { font-family: 'Cinzel', serif; font-size: 1.3rem; margin: 0; color: var(--accent-color); }
        .modal-magic-actions { display: flex; gap: 0.8rem; margin-top: 1.5rem; justify-content: center; }
        .btn-magic-primary, .btn-magic-danger, .btn-magic-secondary { padding: 0.6rem 1.5rem; font-family: 'Cinzel', serif; font-weight: 700; cursor: pointer; border-radius: 0.4rem; font-size: 0.9rem; }
        .btn-magic-primary { background: var(--accent-color); color: #fff; border: 1px solid var(--gold-color); }
        .btn-magic-danger { background: #741b1b; color: #fff; border: 1px solid var(--gold-color); }
        .btn-magic-secondary { background: transparent; color: var(--text-color); border: 1px solid var(--border-color); }

        .btn-danger {
            background: #741b1b; color: white; border: none; padding: 0.5rem 1rem;
            border-radius: 0.4rem; cursor: pointer; font-weight: 700; font-size: 0.85rem;
        }

        .danger-zone-footer { margin-top: 3rem; padding-bottom: 2rem; }
        .magical-separator { border: 0; height: 1px; background-image: linear-gradient(to right, transparent, var(--border-color), transparent); margin-bottom: 1.5rem; }
        .btn-sm { padding: 0.4rem 0.8rem; }

        @media (max-width: 900px) {
            .details-content-grid { grid-template-columns: 1fr; }
        }
    </style>
</div>
