<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Competition;
use App\Models\Registration;
use App\Models\CompetitionType;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\RegistrationExporter;
use App\Filament\Resources\RegistrationResource\Pages;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';
    protected static ?string $navigationLabel = 'Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Status Pendaftaran')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])->required(),
                        Forms\Components\Placeholder::make('team_name')
                            ->label('Nama Tim')
                            ->content(fn(?Registration $record): string => $record?->team?->name ?? 'Tidak Ditemukan'),
                        Forms\Components\Placeholder::make('competition_name')
                            ->label('Kompetisi')
                            ->content(fn(?Registration $record): string => $record?->competition?->name ?? 'Tidak Ditemukan'),
                        Forms\Components\TextInput::make('payment_unique_code')->required()->disabled(),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('team.name')
                    ->label('Nama Tim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('participant.full_name')
                    ->label('Nama Leader')
                    ->searchable(),
                Tables\Columns\TextColumn::make('competition.name')
                    ->label('Kompetisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Submit')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft_step_1' => 'Draf (Step 1)',
                        'draft_step_2' => 'Draf (Step 2)',
                        'draft_step_3' => 'Menunggu Pembayaran',
                        'draft_step_4' => 'Menunggu Finalisasi',
                        'pending' => 'Pending',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })->color(fn(string $state): string => match ($state) {
                        'draft_step_1', 'draft_step_2', 'draft_step_3' => 'info',
                        'draft_step_4' => 'primary',
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })->sortable(),
                Tables\Columns\TextColumn::make('payment_unique_code')
                    ->label('Kode Pembayaran')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Durasi di Status Ini')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('competition.competitionType.type')
                    ->label('Tipe Lomba')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])


            ->filters([
                Tables\Filters\SelectFilter::make('competition_id')
                    ->label('Kompetisi')
                    ->options(Competition::all()->pluck('name', 'id')->toArray())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('competition_type')
                    ->label('Jenis Kompetisi')
                    ->options([
                        'individu' => 'Individu',
                        'group-2-orang' => 'Grup 2 Orang',
                        'group-3-orang' => 'Grup 3 Orang',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'];
                        if (empty($value)) {
                            return $query;
                        }
                        return $query->whereHas('competition.competitionType', function (Builder $query) use ($value) {
                            $query->where('type', $value);
                        });
                    }),


                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft_step_1' => 'Draf (Step 1)',
                        'draft_step_2' => 'Draf (Step 2)',
                        'draft_step_3' => 'Menunggu Pembayaran',
                        'draft_step_4' => 'Menunggu Finalisasi',
                        'pending' => 'Pending',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(function (Registration $record) {
                        $protectedStatuses = ['draft_step_4', 'pending', 'verified'];
                        return in_array($record->status, $protectedStatuses);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('verify')
                        ->label('Verifikasi Terpilih')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'verified']))
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    BulkAction::make('reject')
                        ->label('Tolak Terpilih')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'rejected']))
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-x-circle'),
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $protectedStatuses = ['draft_step_4', 'pending', 'verified'];
                            $hasProtectedRecords = $records->contains(fn ($record) => in_array($record->status, $protectedStatuses));
                            if ($hasProtectedRecords) {
                                Notification::make()
                                    ->title('HAPUS DATA DITOLAK')
                                    ->body('Tidak dapat menghapus karena salah satu data yang dipilih sudah dalam tahap pembayaran atau final.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $records->each->delete();
                        }),
                    ExportBulkAction::make()
                        ->label('Export Terpilih ke Excel')
                        ->exporter(RegistrationExporter::class),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }
}
