<?php

namespace App\Filament\Resources\TournamentParticipants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TournamentParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')->label('Torneo')->relationship('tournament', 'name')->required()->searchable(),
                Select::make('user_id')
                    ->label('Titular de la cuenta')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} · {$record->phone}")
                    ->required()
                    ->searchable(),
                TextInput::make('entry_name')->label('Nombre de jugada')->maxLength(60),
                Select::make('status')->label('Estado')->options([
                    'pending_payment' => 'Pendiente de pago',
                    'approved' => 'Aprobado',
                    'rejected' => 'Rechazado',
                    'suspended' => 'Suspendido',
                ])->required(),
                Select::make('payment_status')->label('Estado de pago')->options([
                    'unpaid' => 'No pagado',
                    'pending_review' => 'Revision pendiente',
                    'paid' => 'Pagado',
                    'waived' => 'Exonerado',
                ])->required(),
                DateTimePicker::make('paid_at')->label('Fecha de pago'),
                DateTimePicker::make('approved_at')->label('Fecha de aprobacion'),
                Select::make('approved_by')->label('Aprobado por')->relationship('approver', 'name')->searchable(),
                Placeholder::make('proof_display')
                    ->label('Comprobante de pago')
                    ->content(function ($record): HtmlString {
                        if (! $record?->id || ! $record->payment_proof_path) {
                            return new HtmlString('<em class="text-sm text-gray-400">Sin comprobante adjunto</em>');
                        }

                        $url = route('participants.proof', $record);

                        return new HtmlString(
                            '<a href="' . e($url) . '" target="_blank" rel="noopener" title="Ver comprobante">'
                            . '<img src="' . e($url) . '" alt="Comprobante de pago"'
                            . ' style="max-height:220px;max-width:100%;border-radius:10px;border:1px solid #e5e7eb;cursor:zoom-in;">'
                            . '</a>'
                            . '<p style="margin-top:6px;font-size:11px;color:#9ca3af;">Clic para abrir en pestaña nueva</p>'
                        );
                    })
                    ->columnSpanFull(),
                Textarea::make('notes')->label('Notas')->columnSpanFull(),
            ]);
    }
}
