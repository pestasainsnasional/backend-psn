<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Notifications\AdminVerificationNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Manajemen Admin';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Administrator';
    protected static ?string $pluralLabel = 'Administrators';
    protected static ?string $label = 'Administrators';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap Admin'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email Admin'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->confirmed()
                    ->maxLength(255)
                    ->label('Password Baru'),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->label('Konfirmasi Password'),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label('Role (Peran)')
                    ->default(fn(string $operation) => $operation === 'create' ? [Role::where('name', 'admin')->first()?->id] : null)
                    ->disabled(fn(string $operation) => $operation === 'edit' && !auth()->user()->hasRole('super_admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Admin'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email Admin'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Terverifikasi')
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(
                        'success'
                    )
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui Pada'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->nullable()
                    ->label('Status Verifikasi Email')
                    ->trueLabel('Terverifikasi')
                    ->falseLabel('Belum Terverifikasi')
                    ->default(''),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('resend_verification')
                        ->label('Kirim Ulang Verifikasi Email')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->hidden(fn(User $record): bool => $record->hasVerifiedEmail())
                        ->action(function (User $record) {
                            $record->notify(new AdminVerificationNotification);

                            Notification::make()
                                ->title('Email Verifikasi Dikirim Ulang')
                                ->body("Email verifikasi telah dikirim ulang ke {$record->email}.")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('resend_verification_bulk')
                        ->label('Kirim Ulang Verifikasi (Pilih)')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(function (Tables\Actions\BulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (!$record->hasVerifiedEmail()) {
                                    $record->notify(new AdminVerificationNotification);;
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title('Email Verifikasi Dikirim Ulang')
                                ->body("Email verifikasi telah dikirim ulang ke {$count} user yang dipilih.")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('admin');
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        if (empty($data['email_verified_at'])) {
            $data['email_verified_at'] = null;
        }
        return $data;
    }

    public static function mutateFormDataBeforeFill(array $data): array
    {
        unset($data['password']);
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        if (empty($data['email_verified_at'])) {
            $data['email_verified_at'] = null;
        }
        return $data;
    }

    protected static function afterCreate(User $record, array $data): void
    {
        if (!in_array(Role::where('name', 'admin')->first()?->id, $data['roles'] ?? [])) {
            $data['roles'][] = Role::where('name', 'admin')->first()?->id;
        }
        $record->syncRoles($data['roles']);
    }

    protected static function afterSave(User $record, array $data): void
    {
        $record->syncRoles($data['roles']);
    }
}
