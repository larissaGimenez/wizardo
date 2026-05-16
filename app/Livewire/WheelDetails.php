<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use App\Models\Spell;
use App\Models\Quest;
use App\Models\Challenge;
use App\Models\WheelSpellCompletion;
use Livewire\WithFileUploads;
use Carbon\Carbon;

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

        $this->applyMissedPenalties();
    }

    /**
     * Aplica penalidades automáticas para feitiços diários não concluídos em dias anteriores
     */
    protected function applyMissedPenalties()
    {
        $dailySpells = $this->wheel->spells()->where('type', 'feitiço diário')->get();
        $totalDamage = 0;
        $missedDaysCount = 0;

        foreach ($dailySpells as $spell) {
            $lastActivity = WheelSpellCompletion::where('wheel_id', $this->wheel->id)
                ->where('spell_id', $spell->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $startDate = $lastActivity 
                ? Carbon::parse(max($lastActivity->last_completed_at, $lastActivity->last_penalty_applied_at))
                : $this->wheel->created_at->startOfDay();

            $yesterday = Carbon::yesterday();

            // Se a última atividade foi antes de ontem, temos dias perdidos
            if ($startDate->lessThan($yesterday)) {
                $daysDiff = $startDate->diffInDays($yesterday);
                $damage = $spell->damage * $daysDiff;
                $totalDamage += $damage;
                $missedDaysCount += $daysDiff;

                // Registra que a penalidade foi aplicada até ontem
                WheelSpellCompletion::updateOrCreate(
                    [
                        'wheel_id' => $this->wheel->id,
                        'spell_id' => $spell->id,
                        'last_completed_at' => $startDate->toDateString(), // Mantém a última conclusão
                    ],
                    [
                        'last_penalty_applied_at' => $yesterday->toDateString()
                    ]
                );
            }
        }

        if ($totalDamage > 0) {
            $newXp = max(0, $this->wheel->xp - $totalDamage);
            $this->wheel->update(['xp' => $newXp]);
            session()->flash('error', "⚡ Você esqueceu de seus feitiços! Penalidade de -$totalDamage XP aplicada por $missedDaysCount dia(s) perdido(s).");
        }
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
        // Se for feitiço diário, verifica se já foi feito hoje
        if ($spell->type === 'feitiço diário') {
            if ($this->wheel->isSpellCompletedToday($spell->id)) {
                session()->flash('error', "Este feitiço já foi realizado hoje!");
                return;
            }

            // Registra conclusão
            WheelSpellCompletion::updateOrCreate(
                [
                    'wheel_id' => $this->wheel->id,
                    'spell_id' => $spell->id,
                    'last_completed_at' => Carbon::today()->toDateString(),
                ]
            );
        }

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
        if ($challenge->level != $this->wheel->level + 1) {
            session()->flash('error', "Você ainda não pode realizar este desafio!");
            return;
        }

        if (!$this->wheel->isNextChallengeUnlocked()) {
            session()->flash('error', "XP insuficiente para este desafio!");
            return;
        }

        $challenge->update(['is_completed' => true]);
        $this->wheel->increment('level');
        
        session()->flash('message', "PARABÉNS! Você alcançou o Nível " . $this->wheel->level . "!");
    }

    public function resetProgress()
    {
        $this->wheel->update(['level' => 0, 'xp' => 0]);
        $this->wheel->challenges()->update(['is_completed' => false]);
        WheelSpellCompletion::where('wheel_id', $this->wheel->id)->delete();

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
