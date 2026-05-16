<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Wheel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'level',
        'xp',
    ];

    // Pontos necessários PARA CADA NÍVEL (conforme imagem)
    public static $xpTable = [
        1 => 200,
        2 => 400,
        3 => 600,
        4 => 800,
        5 => 1000,
        6 => 1200,
        7 => 1400,
        8 => 1400,
        9 => 1500,
        10 => 1500,
    ];

    public function spells(): HasMany
    {
        return $this->hasMany(Spell::class);
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(WheelSpellCompletion::class);
    }

    public function levelTitles(): HasMany
    {
        return $this->hasMany(LevelTitle::class);
    }

    /**
     * Título do nível atual
     */
    public function getLevelTitleAttribute()
    {
        $titleRecord = $this->levelTitles()->where('level', $this->level)->first();
        return $titleRecord ? $titleRecord->title : 'Iniciante';
    }

    /**
     * Pontos totais acumulados necessários para o PRÓXIMO nível
     */
    public function getXpRequiredForNextLevelAttribute()
    {
        if ($this->level >= 10) return 10000;

        $total = 0;
        for ($i = 1; $i <= ($this->level + 1); $i++) {
            $total += self::$xpTable[$i];
        }
        return $total;
    }

    /**
     * Pontos totais acumulados necessários para o nível ATUAL
     */
    public function getXpRequiredForCurrentLevelAttribute()
    {
        if ($this->level == 0) return 0;

        $total = 0;
        for ($i = 1; $i <= $this->level; $i++) {
            $total += self::$xpTable[$i];
        }
        return $total;
    }

    /**
     * Verifica se o desafio do próximo nível está desbloqueado (pela XP)
     */
    public function isNextChallengeUnlocked()
    {
        if ($this->level >= 10) return false;
        return $this->xp >= $this->xp_required_for_next_level;
    }

    /**
     * Progresso em porcentagem para o próximo nível
     */
    public function getLevelProgressPercentageAttribute()
    {
        if ($this->level >= 10) return 100;

        $currentLevelXp = $this->xp_required_for_current_level;
        $nextLevelXp = $this->xp_required_for_next_level;
        
        $xpInCurrentLevel = $this->xp - $currentLevelXp;
        $xpNeededForNext = $nextLevelXp - $currentLevelXp;

        if ($xpNeededForNext <= 0) return 0;

        return min(100, max(0, ($xpInCurrentLevel / $xpNeededForNext) * 100));
    }

    /**
     * Check if a spell is completed today
     */
    public function isSpellCompletedToday($spellId)
    {
        return $this->completions()
            ->where('spell_id', $spellId)
            ->where('last_completed_at', Carbon::today()->toDateString())
            ->exists();
    }
}
