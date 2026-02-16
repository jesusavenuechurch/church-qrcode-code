<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Agent;
use App\Models\AgentCertification;

class AgentCertificationPage extends Page
{
    protected static string $view = 'filament.pages.agent-certification';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Certification';
    protected static ?string $title = 'Partner Certification';
    protected static ?int $navigationSort = -10;

    // Form state — one property per question
    public ?string $q1  = null;
    public ?string $q2  = null;
    public ?string $q3  = null;
    public ?string $q4  = null;
    public ?string $q5  = null;
    public ?string $q6  = null;
    public ?string $q7  = null;
    public ?string $q8  = null;
    public ?string $q9  = null;
    public ?string $q10 = null;
    public ?string $q11 = null;

    // Final acknowledgment checkboxes
    public bool $ack1 = false;
    public bool $ack2 = false;
    public bool $ack3 = false;
    public bool $ack4 = false;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSalesAgent() ?? false;
    }

    public function getAgent(): ?Agent
    {
        return Agent::where('email', Auth::user()->email)->first();
    }

    public function getCertification(): ?AgentCertification
    {
        $agent = $this->getAgent();
        return $agent ? AgentCertification::where('agent_id', $agent->id)->first() : null;
    }

    /**
     * Answer key — correct answer for each question
     */
    protected function getAnswerKey(): array
    {
        return [
            'q1'  => 'B', // Per-event packages
            'q2'  => 'B', // Corporate, private & entertainment events
            'q3'  => 'B', // Event access, registration & participation tracking
            'q4'  => 'A', // Corporate HR department
            'q5'  => 'A', // More likely to convert
            'q6'  => 'B', // Once on first paid package
            'q7'  => 'B', // After payment verification and month-end
            'q8'  => 'B', // No — commission doesn't recur
            'q9'  => 'A', // Understand commission once per org
            'q10' => 'A', // Commission via referral link only
            'q11' => 'A', // VENTIQ audits suspicious referrals
        ];
    }

    public function submit(): void
    {
        $agent = $this->getAgent();

        if (!$agent) {
            Notification::make()->title('Agent profile not found.')->danger()->send();
            return;
        }

        // All acknowledgments must be checked
        if (!$this->ack1 || !$this->ack2 || !$this->ack3 || !$this->ack4) {
            Notification::make()
                ->title('Please confirm all acknowledgments before submitting.')
                ->warning()
                ->send();
            return;
        }

        // Grade answers
        $answerKey = $this->getAnswerKey();
        $score = 0;
        $total = count($answerKey);

        foreach ($answerKey as $question => $correctAnswer) {
            if ($this->$question === $correctAnswer) {
                $score++;
            }
        }

        $percentage = ($score / $total) * 100;
        $passed     = $percentage >= 75;

        // Upsert certification record
        AgentCertification::updateOrCreate(
            ['agent_id' => $agent->id],
            [
                'score'             => $score,
                'total_questions'   => $total,
                'passed'            => $passed,
                'attempts'          => \DB::raw('attempts + 1'),
                'last_attempted_at' => now(),
                'completed_at'      => $passed ? now() : null,
            ]
        );

        if ($passed) {
            Notification::make()
                ->title("🎉 Certified! You scored {$score}/{$total} ({$percentage}%)")
                ->body('Your referral link is now active.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("Score: {$score}/{$total} (" . round($percentage) . "%) — 75% required to pass.")
                ->body('Review the modules and try again.')
                ->warning()
                ->send();
        }
    }
}