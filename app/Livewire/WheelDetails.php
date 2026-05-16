<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use App\Models\Spell;
use App\Models\Quest;
use App\Models\Challenge;
use Livewire\WithFileUploads;

class WheelDetails extends Component
{
    use WithFileUploads;

    public Wheel $wheel;
    public $newName;
    public $newDescription;
    public $isEditingName = false;
    public $isEditingDescription = false;
    public $showConfirmModal = false;
    public $showDeleteModal = false;
    public $fieldToSave = '';

    public function mount(Wheel $wheel)
    {
        $this->wheel = $wheel;
        $this->newName = $wheel->name;
        $this->newDescription = $wheel->description;
    }

    public function startEditingName()
    {
        $this->isEditingName = true;
        $this->isEditingDescription = false;
    }

    public function startEditingDescription()
    {
        $this->isEditingDescription = true;
        $this->isEditingName = false;
    }

    public function cancelEditing()
    {
        $this->isEditingName = false;
        $this->isEditingDescription = false;
        $this->newName = $this->wheel->name;
        $this->newDescription = $this->wheel->description;
        $this->showConfirmModal = false;
        $this->showDeleteModal = false;
    }

    public function confirmSave($field)
    {
        $this->fieldToSave = $field;

        if ($field === 'name') {
            $this->validate([
                'newName' => 'required|string|max:255|unique:wheels,name,' . $this->wheel->id,
            ]);
            if ($this->newName === $this->wheel->name) {
                $this->isEditingName = false;
                return;
            }
        } else {
            $this->validate([
                'newDescription' => 'nullable|string|max:500',
            ]);
            if ($this->newDescription === $this->wheel->description) {
                $this->isEditingDescription = false;
                return;
            }
        }

        $this->showConfirmModal = true;
    }

    public function save()
    {
        if ($this->fieldToSave === 'name') {
            $this->wheel->update(['name' => $this->newName]);
            $this->isEditingName = false;
            session()->flash('message', 'Nome da roda atualizado com sucesso!');
        } else {
            $this->wheel->update(['description' => $this->newDescription]);
            $this->isEditingDescription = false;
            session()->flash('message', 'Descrição da roda atualizada com sucesso!');
        }

        $this->showConfirmModal = false;
    }

    public function useSpell(Spell $spell)
    {
        $points = $spell->gain > 0 ? $spell->gain : -($spell->damage);
        
        $newXp = max(0, min(10000, $this->wheel->xp + $points));
        $this->wheel->update(['xp' => $newXp]);
        
        session()->flash('message', "Feitiço executado! XP: " . ($points > 0 ? "+$points" : "$points"));
    }

    public function completeQuest(Quest $quest)
    {
        $points = $quest->gain;
        $newXp = max(0, min(10000, $this->wheel->xp + $points));
        $this->wheel->update(['xp' => $newXp]);
        
        session()->flash('message', "Missão cumprida! +$points XP");
    }

    public function completeChallenge(Challenge $challenge)
    {
        // Só pode completar o desafio se for o do nível seguinte
        if ($challenge->level != $this->wheel->level + 1) {
            session()->flash('error', "Você ainda não pode realizar este desafio!");
            return;
        }

        // Só pode completar se tiver a XP necessária
        if (!$this->wheel->isNextChallengeUnlocked()) {
            session()->flash('error', "XP insuficiente para este desafio!");
            return;
        }

        $challenge->update(['is_completed' => true]);
        
        // Sobe de nível
        $this->wheel->increment('level');
        
        session()->flash('message', "PARABÉNS! Você alcançou o Nível " . $this->wheel->level . "!");
    }

    public function resetProgress()
    {
        $this->wheel->update([
            'level' => 0,
            'xp' => 0
        ]);

        $this->wheel->challenges()->update(['is_completed' => false]);

        session()->flash('message', 'Progresso da roda resetado com sucesso!');
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $this->wheel->delete();
        return redirect()->route('wheel.manager')->with('message', 'Roda excluída com sucesso!');
    }

    public function render()
    {
        return view('livewire.wheel-details', [
            'spells' => $this->wheel->spells()->get(),
            'challenges' => $this->wheel->challenges()->orderBy('level')->get(),
            'quests' => $this->wheel->quests()->get(),
        ])->extends('layouts.app')->section('content');
    }
}
