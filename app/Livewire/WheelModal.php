<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use App\Models\Spell;
use App\Models\Quest;
use App\Models\Challenge;
use Livewire\Attributes\On;

class WheelModal extends Component
{
    public $showModal = false;
    public $isEditing = false;
    public $editingWheelId;
    public $currentStep = 1;
    
    // Step 1: Info
    public $name;
    public $description;
    public $levelTitles = [];

    // Step 2: Spells
    public $selected_spells = [];
    public $new_spell_name, $new_spell_action, $new_spell_gain, $new_spell_damage, $new_spell_type = 'Gain';

    // Step 3: Quests
    public $selected_quests = [];
    public $new_quest_name, $new_quest_description, $new_quest_gain;

    // Step 4: Challenges
    public $selected_challenges = [];
    public $new_challenge_name, $new_challenge_description, $new_challenge_level = 1, $new_challenge_prize_name, $new_challenge_prize_description;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'levelTitles.*' => 'required|string|max:255',
        ];
    }

    #[On('open-wheel-modal')]
    public function openModal($wheelId = null)
    {
        $this->resetForm();
        
        if ($wheelId) {
            $this->edit($wheelId);
        } else {
            $this->loadLevelTitles();
            $this->showModal = true;
        }
    }

    public function loadLevelTitles($wheelId = null)
    {
        $titles = collect();
        if ($wheelId) {
            $titles = \App\Models\LevelTitle::where('wheel_id', $wheelId)->get()->keyBy('level');
        }
        
        for ($i = 1; $i <= 10; $i++) {
            $this->levelTitles[$i] = $titles->has($i) ? $titles[$i]->title : "Nível {$i}";
        }
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validateOnly('name');
            // optionally validate levelTitles
        }
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $wheel = Wheel::findOrFail($id);
        $this->editingWheelId = $id;
        $this->name = $wheel->name;
        $this->description = $wheel->description;
        $this->selected_spells = $wheel->spells->pluck('id')->toArray();
        $this->selected_quests = $wheel->quests->pluck('id')->toArray();
        $this->selected_challenges = $wheel->challenges->pluck('id')->toArray();
        $this->loadLevelTitles($id);
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function createSpell()
    {
        $this->validate([
            'new_spell_name' => 'required|string|max:255',
            'new_spell_action' => 'required|string|max:255',
            'new_spell_gain' => 'nullable|numeric',
            'new_spell_damage' => 'nullable|numeric',
            'new_spell_type' => 'required|string|max:50',
        ]);

        $spell = Spell::create([
            'name' => $this->new_spell_name,
            'action' => $this->new_spell_action,
            'gain' => $this->new_spell_gain ?? 0,
            'damage' => $this->new_spell_damage ?? 0,
            'type' => $this->new_spell_type,
        ]);

        $this->selected_spells[] = $spell->id;
        
        $this->new_spell_name = '';
        $this->new_spell_action = '';
        $this->new_spell_gain = '';
        $this->new_spell_damage = '';
        $this->new_spell_type = 'Gain';
    }

    public function createQuest()
    {
        $this->validate([
            'new_quest_name' => 'required|string|max:255',
            'new_quest_description' => 'required|string',
            'new_quest_gain' => 'required|numeric',
        ]);

        $quest = Quest::create([
            'name' => $this->new_quest_name,
            'description' => $this->new_quest_description,
            'gain' => $this->new_quest_gain,
        ]);

        $this->selected_quests[] = $quest->id;

        $this->new_quest_name = '';
        $this->new_quest_description = '';
        $this->new_quest_gain = '';
    }

    public function createChallenge()
    {
        $this->validate([
            'new_challenge_name' => 'required|string|max:255',
            'new_challenge_description' => 'required|string',
            'new_challenge_level' => 'required|integer|min:1|max:10',
            'new_challenge_prize_name' => 'required|string|max:255',
            'new_challenge_prize_description' => 'nullable|string',
        ]);

        $challenge = Challenge::create([
            'name' => $this->new_challenge_name,
            'description' => $this->new_challenge_description,
            'level' => $this->new_challenge_level,
            'prize_name' => $this->new_challenge_prize_name,
            'prize_description' => $this->new_challenge_prize_description,
            'is_completed' => false,
        ]);

        $this->selected_challenges[] = $challenge->id;

        $this->new_challenge_name = '';
        $this->new_challenge_description = '';
        $this->new_challenge_level = 1;
        $this->new_challenge_prize_name = '';
        $this->new_challenge_prize_description = '';
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:wheels,name,' . $this->editingWheelId,
            'description' => 'nullable|string|max:500',
        ]);

        if ($this->isEditing) {
            $wheel = Wheel::find($this->editingWheelId);
            $wheel->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            
            // Sync Spells
            $currentSpells = $wheel->spells->pluck('id')->toArray();
            $toDisassociate = array_diff($currentSpells, $this->selected_spells);
            Spell::whereIn('id', $toDisassociate)->update(['wheel_id' => null]);
            if (!empty($this->selected_spells)) {
                Spell::whereIn('id', $this->selected_spells)->update(['wheel_id' => $wheel->id]);
            }

            // Sync Quests
            $currentQuests = $wheel->quests->pluck('id')->toArray();
            $questsToDisassociate = array_diff($currentQuests, $this->selected_quests);
            Quest::whereIn('id', $questsToDisassociate)->update(['wheel_id' => null]);
            if (!empty($this->selected_quests)) {
                Quest::whereIn('id', $this->selected_quests)->update(['wheel_id' => $wheel->id]);
            }

            // Sync Challenges
            $currentChallenges = $wheel->challenges->pluck('id')->toArray();
            $challengesToDisassociate = array_diff($currentChallenges, $this->selected_challenges);
            Challenge::whereIn('id', $challengesToDisassociate)->update(['wheel_id' => null]);
            if (!empty($this->selected_challenges)) {
                Challenge::whereIn('id', $this->selected_challenges)->update(['wheel_id' => $wheel->id]);
            }
            
            session()->flash('message', 'Roda atualizada com sucesso!');
        } else {
            $wheel = Wheel::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            
            if (!empty($this->selected_spells)) {
                Spell::whereIn('id', $this->selected_spells)->update(['wheel_id' => $wheel->id]);
            }
            if (!empty($this->selected_quests)) {
                Quest::whereIn('id', $this->selected_quests)->update(['wheel_id' => $wheel->id]);
            }
            if (!empty($this->selected_challenges)) {
                Challenge::whereIn('id', $this->selected_challenges)->update(['wheel_id' => $wheel->id]);
            }
            
            session()->flash('message', 'Roda cadastrada com sucesso!');
        }

        // Salvar Level Titles
        foreach ($this->levelTitles as $level => $title) {
            \App\Models\LevelTitle::updateOrCreate(
                ['wheel_id' => $wheel->id, 'level' => $level],
                ['title' => $title]
            );
        }

        $this->dispatch('wheel-saved');
        $this->closeModal();
    }

    private function resetForm()
    {
        $this->currentStep = 1;
        $this->name = '';
        $this->description = '';
        $this->levelTitles = [];
        $this->selected_spells = [];
        $this->selected_quests = [];
        $this->selected_challenges = [];
        
        $this->new_spell_name = ''; $this->new_spell_action = ''; $this->new_spell_gain = ''; $this->new_spell_damage = ''; $this->new_spell_type = 'Gain';
        $this->new_quest_name = ''; $this->new_quest_description = ''; $this->new_quest_gain = '';
        $this->new_challenge_name = ''; $this->new_challenge_description = ''; $this->new_challenge_level = 1; $this->new_challenge_prize_name = ''; $this->new_challenge_prize_description = '';

        $this->editingWheelId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $spellsQuery = Spell::with('wheel')->whereNull('wheel_id');
        if ($this->isEditing) { $spellsQuery->orWhere('wheel_id', $this->editingWheelId); }

        $questsQuery = Quest::with('wheel')->whereNull('wheel_id');
        if ($this->isEditing) { $questsQuery->orWhere('wheel_id', $this->editingWheelId); }

        $challengesQuery = Challenge::with('wheel')->whereNull('wheel_id');
        if ($this->isEditing) { $challengesQuery->orWhere('wheel_id', $this->editingWheelId); }

        return view('livewire.wheel-modal', [
            'all_spells' => $spellsQuery->get(),
            'all_quests' => $questsQuery->get(),
            'all_challenges' => $challengesQuery->get(),
        ]);
    }
}
