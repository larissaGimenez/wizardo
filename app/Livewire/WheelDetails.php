<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use App\Models\Spell;
use App\Models\Quest;
use App\Models\Challenge;
use App\Models\WheelSpellCompletion;
use App\Models\WheelActionHistory;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    // Habit Tracker State
    public $showTracker = false;
    public $trackerMonth;
    public $trackerYear;

    public function mount(Wheel $wheel)
    {
        $this->wheel = $wheel;
        $this->newName = $wheel->name;
        $this->newDescription = $wheel->description;
        $this->trackerMonth = now()->month;
        $this->trackerYear = now()->year;

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

            if ($lastActivity) {
                $completed = $lastActivity->last_completed_at;
                $penalty = $lastActivity->last_penalty_applied_at;

                if ($completed && $penalty) {
                    $startDate = $completed->greaterThan($penalty) ? $completed : $penalty;
                } else {
                    $startDate = $completed ?? $penalty;
                }
                $startDate = Carbon::parse($startDate);
            } else {
                $startDate = $this->wheel->created_at->startOfDay();
            }

            $yesterday = Carbon::yesterday();

            if ($startDate->lessThan($yesterday)) {
                $daysDiff = $startDate->diffInDays($yesterday);
                $damage = $spell->damage * $daysDiff;
                $totalDamage += $damage;
                $missedDaysCount += $daysDiff;

                WheelSpellCompletion::updateOrCreate(
                    [
                        'wheel_id' => $this->wheel->id,
                        'spell_id' => $spell->id,
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
        if ($spell->type === 'feitiço diário') {
            if ($this->wheel->isSpellCompletedToday($spell->id)) {
                session()->flash('error', "Este feitiço já foi realizado hoje!");
                return;
            }

            WheelSpellCompletion::updateOrCreate(
                [
                    'wheel_id' => $this->wheel->id,
                    'spell_id' => $spell->id,
                ],
                [
                    'last_completed_at' => Carbon::today()->toDateString(),
                ]
            );
        }

        $points = $spell->gain > 0 ? $spell->gain : -($spell->damage);
        
        $newXp = max(0, min(10000, $this->wheel->xp + $points));
        $this->wheel->update(['xp' => $newXp]);

        // Grava histórico
        WheelActionHistory::create([
            'wheel_id' => $this->wheel->id,
            'actionable_id' => $spell->id,
            'actionable_type' => Spell::class,
            'points' => $points,
        ]);
        
        session()->flash('message', "Feitiço executado! XP: " . ($points > 0 ? "+$points" : "$points"));
    }

    public function completeQuest(Quest $quest)
    {
        $points = $quest->gain;
        $newXp = max(0, min(10000, $this->wheel->xp + $points));
        $this->wheel->update(['xp' => $newXp]);

        // Grava histórico
        WheelActionHistory::create([
            'wheel_id' => $this->wheel->id,
            'actionable_id' => $quest->id,
            'actionable_type' => Quest::class,
            'points' => $points,
        ]);
        
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
        WheelActionHistory::where('wheel_id', $this->wheel->id)->delete();

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

    // Tracker Methods
    public function toggleTracker()
    {
        $this->showTracker = !$this->showTracker;
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->trackerYear, $this->trackerMonth, 1)->subMonth();
        $this->trackerMonth = $date->month;
        $this->trackerYear = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->trackerYear, $this->trackerMonth, 1)->addMonth();
        $this->trackerMonth = $date->month;
        $this->trackerYear = $date->year;
    }

    public function render()
    {
        // Histórico de hoje
        $todayHistory = WheelActionHistory::where('wheel_id', $this->wheel->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        // Totais por seção
        $dailySpellsXpTotal = $todayHistory->where('actionable_type', Spell::class)
            ->filter(fn($h) => $h->actionable && $h->actionable->type === 'feitiço diário')
            ->sum('points');

        $penaltiesToday = $todayHistory->where('actionable_type', Spell::class)
            ->filter(fn($h) => $h->actionable && $h->actionable->type === 'penalidade das trevas');
        
        $questsToday = $todayHistory->where('actionable_type', Quest::class);

        // Contagem por item individual (para mostrar nos cards)
        $itemCounts = WheelActionHistory::where('wheel_id', $this->wheel->id)
            ->whereDate('created_at', Carbon::today())
            ->select('actionable_id', 'actionable_type', DB::raw('count(*) as total'))
            ->groupBy('actionable_id', 'actionable_type')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->actionable_type . ':' . $item->actionable_id => $item->total];
            })
            ->toArray();

        // Data for Habit Tracker
        $trackerDate = Carbon::create($this->trackerYear, $this->trackerMonth, 1);
        $daysInMonth = $trackerDate->daysInMonth;
        
        $trackerSpells = $this->wheel->spells()->get();
        $trackerQuests = $this->wheel->quests()->get();
        
        $completions = WheelActionHistory::where('wheel_id', $this->wheel->id)
            ->whereYear('created_at', $this->trackerYear)
            ->whereMonth('created_at', $this->trackerMonth)
            ->get()
            ->groupBy(function($h) {
                return $h->actionable_type . '_' . $h->actionable_id . '_' . $h->created_at->day;
            });

        return view('livewire.wheel-details', [
            'spells' => $this->wheel->spells()->get(),
            'challenges' => $this->wheel->challenges()->orderBy('level')->get(),
            'quests' => $this->wheel->quests()->get(),
            'dailySpellsXp' => $dailySpellsXpTotal,
            'penaltiesCount' => $penaltiesToday->count(),
            'penaltiesDamage' => $penaltiesToday->sum('points'),
            'questsCount' => $questsToday->count(),
            'questsGain' => $questsToday->sum('points'),
            'itemCounts' => $itemCounts,
            // Tracker Data
            'daysInMonth' => $daysInMonth,
            'trackerSpells' => $trackerSpells,
            'trackerQuests' => $trackerQuests,
            'trackerCompletions' => $completions,
            'trackerDateTitle' => $trackerDate->translatedFormat('F Y'),
        ])->extends('layouts.app')->section('content');
    }
}
