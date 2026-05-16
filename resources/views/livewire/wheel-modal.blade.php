<div>
    @if($showModal)
        @teleport('body')
            <div class="modal-backdrop-mystic full-screen-modal" wire:click="closeModal">
                <div class="modal-parchment-full" wire:click.stop>
                    <div class="parchment-texture"></div>
                    <div class="modal-header-mystic">
                        <div class="header-content-wrapper">
                            <h2>{{ $isEditing ? 'Reescrever Destino' : 'Conjurar Nova Roda' }}</h2>
                        </div>
                        <button wire:click="closeModal" class="close-btn-mystic">✕</button>
                    </div>
                    <div class="modal-main-layout">
                        <!-- Vertical Wizard Stepper -->
                        <div class="wizard-stepper-vertical">
                            <div class="step {{ $currentStep >= 1 ? 'active' : '' }}">
                                <div class="step-icon">1</div>
                                <span class="step-label">Essência</span>
                            </div>
                            <div class="step-line-vertical {{ $currentStep >= 2 ? 'active' : '' }}"></div>
                            <div class="step {{ $currentStep >= 2 ? 'active' : '' }}">
                                <div class="step-icon">2</div>
                                <span class="step-label">Feitiços</span>
                            </div>
                            <div class="step-line-vertical {{ $currentStep >= 3 ? 'active' : '' }}"></div>
                            <div class="step {{ $currentStep >= 3 ? 'active' : '' }}">
                                <div class="step-icon">3</div>
                                <span class="step-label">Missões</span>
                            </div>
                            <div class="step-line-vertical {{ $currentStep >= 4 ? 'active' : '' }}"></div>
                            <div class="step {{ $currentStep >= 4 ? 'active' : '' }}">
                                <div class="step-icon">4</div>
                                <span class="step-label">Desafios</span>
                            </div>
                        </div>

                        <form wire:submit.prevent="save" class="modal-form-full">
                            <div class="modal-body-mystic scrollable-content">
                                
                                <!-- Step 1: Info & Levels -->
                                @if($currentStep == 1)
                                <div class="step-content fade-in">
                                    <h3 class="section-subtitle text-center compact-title">A Essência da Roda</h3>
                                    <div class="form-grid-2col compact">
                                        <div class="left-col-compact">
                                            <div class="form-group-parchment compact-group">
                                                <label>Nome da Roda</label>
                                                <input type="text" wire:model="name" class="input-parchment input-sm" placeholder="Nomeie sua criação...">
                                                @error('name') <span class="error-magical">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="form-group-parchment compact-group">
                                                <label>Descrição e Propósito</label>
                                                <textarea wire:model="description" class="input-parchment input-sm textarea-compact" rows="3" placeholder="Descreva o propósito desta roda..."></textarea>
                                                @error('description') <span class="error-magical">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="right-col-compact">
                                            <h4 class="mini-title">Títulos dos Níveis</h4>
                                            <div class="levels-grid compact-levels">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <div class="level-input-group">
                                                        <span class="level-num">{{ $i }}</span>
                                                        <input type="text" wire:model="levelTitles.{{ $i }}" class="input-parchment input-xs" placeholder="Título Nível {{ $i }}">
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Step 2: Spells -->
                                @if($currentStep == 2)
                                <div class="step-content fade-in">
                                    <h3 class="section-subtitle text-center compact-title">Feitiços do Grimório</h3>
                                    <div class="wizard-split-layout compact">
                                        <div class="wizard-list-side">
                                            <h4 class="mini-title">Vincular Existentes</h4>
                                            <div class="item-select-scroll custom-scroll">
                                                @forelse($all_spells as $spell)
                                                    <label class="item-option-magical compact" wire:key="spell-{{ $spell->id }}">
                                                        <div class="checkbox-wrapper xs">
                                                            <input type="checkbox" wire:model="selected_spells" value="{{ $spell->id }}">
                                                            <div class="custom-checkbox"></div>
                                                        </div>
                                                        <div class="item-info compact">
                                                            <span class="item-name">{{ $spell->name }}</span>
                                                            <span class="item-tag level">{{ $spell->action }}</span>
                                                        </div>
                                                    </label>
                                                @empty
                                                    <p class="no-items-msg">Nenhum feitiço encontrado.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="wizard-create-side">
                                            <h4 class="mini-title">Criar Novo Feitiço</h4>
                                            <div class="create-form-box compact">
                                                <input type="text" wire:model="new_spell_name" class="input-parchment input-xs" placeholder="Feitiço">
                                                <input type="text" wire:model="new_spell_action" class="input-parchment input-xs" placeholder="Ação bruxa (descrição)">
                                                <select wire:model="new_spell_type" class="input-parchment input-xs">
                                                    <option value="Gain">Feitiço diário</option>
                                                    <option value="Damage">Penalidade das trevas</option>
                                                </select>
                                                <div class="flex-row gap-sm">
                                                    <input type="number" wire:model="new_spell_gain" class="input-parchment input-xs" placeholder="Ganho">
                                                    <input type="number" wire:model="new_spell_damage" class="input-parchment input-xs" placeholder="Dano">
                                                </div>
                                                <button type="button" wire:click="createSpell" class="btn-magical-seal sm full-width mt-1">CRIAR</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Step 3: Quests -->
                                @if($currentStep == 3)
                                <div class="step-content fade-in">
                                    <h3 class="section-subtitle text-center compact-title">Missões de Hogsmeade</h3>
                                    <div class="wizard-split-layout compact">
                                        <div class="wizard-list-side">
                                            <h4 class="mini-title">Vincular Existentes</h4>
                                            <div class="item-select-scroll custom-scroll">
                                                @forelse($all_quests as $quest)
                                                    <label class="item-option-magical compact" wire:key="quest-{{ $quest->id }}">
                                                        <div class="checkbox-wrapper xs">
                                                            <input type="checkbox" wire:model="selected_quests" value="{{ $quest->id }}">
                                                            <div class="custom-checkbox"></div>
                                                        </div>
                                                        <div class="item-info compact">
                                                            <span class="item-name">{{ $quest->name }}</span>
                                                            <span class="item-tag level">+{{ $quest->gain }} XP</span>
                                                        </div>
                                                    </label>
                                                @empty
                                                    <p class="no-items-msg">Nenhuma missão disponível.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="wizard-create-side">
                                            <h4 class="mini-title">Criar Nova Missão</h4>
                                            <div class="create-form-box compact">
                                                <input type="text" wire:model="new_quest_name" class="input-parchment input-xs" placeholder="Nome da Missão">
                                                <textarea wire:model="new_quest_description" class="input-parchment input-xs" rows="2" placeholder="Descrição"></textarea>
                                                <input type="number" wire:model="new_quest_gain" class="input-parchment input-xs" placeholder="Ganho de XP">
                                                <button type="button" wire:click="createQuest" class="btn-magical-seal sm full-width mt-1">CRIAR</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Step 4: Challenges -->
                                @if($currentStep == 4)
                                <div class="step-content fade-in">
                                    <h3 class="section-subtitle text-center compact-title">Desafios de Hogsmeade</h3>
                                    <div class="wizard-split-layout compact">
                                        <div class="wizard-list-side">
                                            <h4 class="mini-title">Vincular Existentes</h4>
                                            <div class="item-select-scroll custom-scroll">
                                                @forelse($all_challenges as $challenge)
                                                    <label class="item-option-magical compact" wire:key="challenge-{{ $challenge->id }}">
                                                        <div class="checkbox-wrapper xs">
                                                            <input type="checkbox" wire:model="selected_challenges" value="{{ $challenge->id }}">
                                                            <div class="custom-checkbox"></div>
                                                        </div>
                                                        <div class="item-info compact">
                                                            <span class="item-name">{{ $challenge->name }}</span>
                                                            <span class="item-tag level">Nível {{ $challenge->level }}</span>
                                                        </div>
                                                    </label>
                                                @empty
                                                    <p class="no-items-msg">Nenhum desafio disponível.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="wizard-create-side">
                                            <h4 class="mini-title">Criar Novo Desafio</h4>
                                            <div class="create-form-box compact">
                                                <div class="flex-row gap-sm">
                                                    <input type="text" wire:model="new_challenge_name" class="input-parchment input-xs" placeholder="Nome do Desafio" style="flex-grow: 1;">
                                                    <input type="number" wire:model="new_challenge_level" class="input-parchment input-xs" placeholder="Nível" min="1" max="10" title="Nível (1-10)" style="width: 60px; text-align: center;">
                                                </div>
                                                <textarea wire:model="new_challenge_description" class="input-parchment input-xs" rows="2" placeholder="Descrição"></textarea>
                                                <input type="text" wire:model="new_challenge_prize_name" class="input-parchment input-xs" placeholder="Nome do Prêmio">
                                                <textarea wire:model="new_challenge_prize_description" class="input-parchment input-xs" rows="2" placeholder="Descrição do Prêmio"></textarea>
                                                <button type="button" wire:click="createChallenge" class="btn-magical-seal sm full-width mt-1">CRIAR</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="modal-footer-mystic compact-footer">
                                <button type="button" wire:click="closeModal" class="btn-cancel-mystic">CANCELAR</button>
                                
                                <div class="wizard-actions">
                                    @if($currentStep > 1)
                                        <button type="button" wire:click="prevStep" class="btn-cancel-mystic">ANTERIOR</button>
                                    @endif

                                    @if($currentStep < 4)
                                        <button type="button" wire:click="nextStep" class="btn-magical-seal">PRÓXIMO ➔</button>
                                    @else
                                        <button type="submit" class="btn-magical-seal">
                                            <div class="seal-glow"></div>
                                            <div class="seal-inner">
                                                <span class="seal-text">{{ $isEditing ? 'CONCLUIR EDIÇÃO' : 'CONJURAR RODA' }}</span>
                                            </div>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <style>
        /* Vertical Layout Modifiers */
        .modal-main-layout {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
        }

        .wizard-stepper-vertical {
            width: 150px;
            background: rgba(0,0,0,0.03);
            border-right: 1px solid rgba(212, 175, 55, 0.2);
            display: flex;
            flex-direction: column;
            padding: 2rem 0;
            align-items: center;
        }

        .wizard-stepper-vertical .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            opacity: 0.4;
            transition: 0.3s;
            cursor: default;
        }
        
        .wizard-stepper-vertical .step.active {
            opacity: 1;
            transform: scale(1.05);
        }

        .wizard-stepper-vertical .step-icon {
            width: 28px;
            height: 28px;
            font-size: 0.85rem;
            border-radius: 50%;
            background: #eadecc;
            border: 2px solid var(--gold-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            color: var(--ink-color);
            transition: 0.3s;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
        }

        .wizard-stepper-vertical .step.active .step-icon {
            background: var(--gold-color);
            color: white;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.6);
        }

        .step-label {
            font-family: 'Cinzel', serif;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ink-color);
        }

        .step-line-vertical {
            width: 2px;
            height: 40px;
            background: rgba(212, 175, 55, 0.2);
            margin: 0.5rem 0;
            transition: 0.3s;
        }

        .step-line-vertical.active {
            background: var(--gold-color);
        }

        /* Compressed Elements */
        .modal-header-mystic { padding: 1.5rem 2rem; display: flex; justify-content: center; position: relative; }
        .modal-header-mystic h2 { font-size: 1.6rem; letter-spacing: 2px; margin: 0; }
        .close-btn-mystic { position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); }

        .modal-form-full { flex-grow: 1; display: flex; flex-direction: column; }
        .scrollable-content { padding: 0.5rem 2rem 1.5rem 2rem; overflow-y: auto; flex-grow: 1; }

        .compact-title { font-size: 1.1rem; margin-top: 0.5rem !important; margin-bottom: 1.5rem !important; }
        .mini-title { font-size: 0.85rem; margin-bottom: 0.8rem; }

        .form-grid-2col.compact { display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; align-items: start; }
        .levels-grid.compact-levels { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.8rem 1.5rem; margin-top: 0.5rem; }
        
        .level-input-group { display: flex; align-items: flex-end; gap: 0.5rem; margin-bottom: 0.5rem; }
        .level-num { font-family: 'Cinzel', serif; font-weight: bold; color: var(--gold-color); font-size: 0.8rem; width: 18px; text-align: right; padding-bottom: 0.2rem; }
        .level-input-group .input-xs { flex-grow: 1; padding: 0.2rem 0; font-size: 0.75rem; margin-bottom: 0; }
        
        .compact-group { margin-bottom: 0.8rem; }
        .compact-group label { margin-bottom: 0.2rem; font-size: 0.7rem; }
        
        .input-sm { padding: 0.3rem 0; font-size: 0.85rem; }
        .input-xs { padding: 0.25rem 0; font-size: 0.8rem; margin-bottom: 0.4rem; }
        .textarea-compact { min-height: 60px; line-height: 1.4; }

        .wizard-split-layout.compact { display: grid; gap: 2rem; grid-template-columns: 1.5fr 1fr; align-items: start; }
        .create-form-box.compact { padding: 1rem; gap: 0.5rem; }

        .item-option-magical.compact { padding: 0.5rem 0.8rem; gap: 0.8rem; margin-bottom: 0.5rem; }
        .checkbox-wrapper.xs { width: 16px; height: 16px; }
        .checkbox-wrapper.xs .custom-checkbox { border-width: 1px; }
        .item-info.compact .item-name { font-size: 0.85rem; }
        .item-info.compact .item-tag { font-size: 0.65rem; }

        .custom-scroll { max-height: calc(100vh - 350px); overflow-y: auto; padding-right: 0.5rem; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(212, 175, 55, 0.5); border-radius: 4px; }

        .flex-row { display: flex; width: 100%; }
        .gap-sm { gap: 0.5rem; }
        .mt-1 { margin-top: 0.5rem; }

        .modal-footer-mystic.compact-footer { padding: 1rem 2rem; gap: 1.5rem; }
        .btn-magical-seal { font-size: 0.75rem; padding: 0.5rem 1.2rem; }
        .btn-cancel-mystic { font-size: 0.85rem; }

        /* Full Screen Modal Styles (Transferred from WheelManager) */
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

        .modal-parchment-full {
            background: #f4ece0;
            width: 94vw;
            height: 94vh;
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
        .header-icon-mystic { font-size: 2rem; }
        .modal-header-mystic h2 {
            font-family: 'Cinzel', serif;
            font-size: 2.2rem;
            color: var(--ink-color);
            margin: 0;
            letter-spacing: 4px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .modal-form-full {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .scrollable-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 3rem 4rem;
        }

        .form-grid-full {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-subtitle {
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
            color: #8b0000;
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(139, 0, 0, 0.2);
            letter-spacing: 2px;
        }

        .form-group-parchment { margin-bottom: 2.5rem; }
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

        .textarea-auto {
            resize: none;
            min-height: 150px;
            line-height: 1.6;
        }

        .decorative-element-parchment {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 4rem;
            opacity: 0.4;
        }
        .decorative-element-parchment .line { height: 1px; flex-grow: 1; background: var(--gold-color); }
        .decorative-element-parchment .symbol { font-size: 1.2rem; color: var(--gold-color); }

        .associations-tabs { display: grid; gap: 2rem; }
        .association-group {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(210, 180, 140, 0.5);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .group-header { display: flex; align-items: center; gap: 0.8rem; margin-bottom: 1.2rem; }
        .group-icon { font-size: 1.2rem; }
        .group-header label { margin: 0; font-size: 0.9rem !important; color: #5d4037 !important; }

        .item-select-scroll {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            max-height: 250px;
            overflow-y: auto;
            padding-right: 1rem;
        }

        .item-option-magical {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1.2rem;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .item-option-magical:hover { background: #fff; border-color: var(--gold-color); transform: translateX(5px); }

        .checkbox-wrapper { position: relative; width: 20px; height: 20px; }
        .checkbox-wrapper input { opacity: 0; position: absolute; cursor: pointer; }
        .custom-checkbox { position: absolute; inset: 0; border: 2px solid var(--gold-color); border-radius: 4px; transition: 0.2s; }
        .checkbox-wrapper input:checked + .custom-checkbox { background: var(--gold-color); box-shadow: 0 0 8px rgba(212, 175, 55, 0.5); }
        .checkbox-wrapper input:checked + .custom-checkbox::after { content: '✓'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 12px; }

        .item-info { display: flex; flex-direction: column; gap: 0.2rem; }
        .item-name { font-family: 'Spectral', serif; font-size: 1rem; font-weight: 500; color: var(--ink-color); }
        .item-tag { font-size: 0.7rem; font-family: 'Cinzel', serif; text-transform: uppercase; }
        .item-tag.current { color: #8b0000; opacity: 0.7; }
        .item-tag.level { color: #5d4037; font-weight: bold; }

        .no-items-msg { font-style: italic; opacity: 0.5; font-size: 0.9rem; grid-column: 1/-1; }

        .modal-footer-mystic {
            padding: 2rem 4rem;
            background: rgba(210, 180, 140, 0.1);
            border-top: 1px dashed rgba(0,0,0,0.1);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 2.5rem;
            position: relative;
            z-index: 10;
        }

        .btn-cancel-mystic { background: none; border: none; font-family: 'Cinzel', serif; font-size: 1rem; letter-spacing: 2px; cursor: pointer; color: #8b0000; opacity: 0.6; transition: 0.3s; }
        .btn-cancel-mystic:hover { opacity: 1; transform: scale(1.05); }

        .close-btn-mystic { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--ink-color); opacity: 0.5; transition: 0.3s; }
        .close-btn-mystic:hover { opacity: 1; color: #8b0000; }

        .error-magical { color: #8b0000; font-size: 0.75rem; font-weight: bold; margin-top: 0.2rem; display: block; }
    </style>
</div>
