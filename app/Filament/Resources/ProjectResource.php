<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationLabel = 'Projects';
    
    protected static ?string $modelLabel = 'Project';
    
    protected static ?string $pluralModelLabel = 'Projects';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Project')
                    ->description('Masukkan detail utama project')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Project')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama project')
                                    ->columnSpan(1),
                                
                                TextInput::make('klien')
                                    ->label('Nama Klien')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama klien')
                                    ->columnSpan(1),
                            ]),
                        
                        RichEditor::make('deskripsi')
                            ->label('Deskripsi Project')
                            ->placeholder('Deskripsikan project secara detail...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Media & Teknologi')
                    ->description('Upload gambar dan tentukan teknologi yang digunakan')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('gambar')
                            ->label('Gambar Project')
                            ->image()
                            ->directory('project-images')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload gambar dengan format JPG, PNG, atau WebP. Maksimal 2MB.')
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stack')
                                    ->label('Teknologi/Stack')
                                    ->placeholder('React, Laravel, MySQL, dll.')
                                    ->helperText('Pisahkan dengan koma untuk multiple teknologi')
                                    ->columnSpan(1),
                                
                                TextInput::make('link')
                                    ->label('Link Project')
                                    ->url()
                                    ->placeholder('https://example.com')
                                    ->helperText('Link demo atau repository (opsional)')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Status & Timeline')
                    ->description('Atur status dan timeline project')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Status Project')
                                    ->options([
                                        'planning' => 'Planning',
                                        'development' => 'Development',
                                        'testing' => 'Testing',
                                        'completed' => 'Completed',
                                        'maintenance' => 'Maintenance',
                                    ])
                                    ->default('planning')
                                    ->required()
                                    ->columnSpan(1),
                                
                                DatePicker::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->default(now())
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),
                                
                                DatePicker::make('tanggal_selesai')
                                    ->label('Tanggal Target Selesai')
                                    ->displayFormat('d/m/Y')
                                    ->after('tanggal_mulai')
                                    ->columnSpan(1),
                            ]),
                        
                        Toggle::make('is_featured')
                            ->label('Featured Project')
                            ->helperText('Tampilkan project ini sebagai featured di homepage')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(url('/images/placeholder-project.png')),
                
                TextColumn::make('nama')
                    ->label('Nama Project')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->description(fn ($record) => $record->klien),
                
                TextColumn::make('klien')
                    ->label('Klien')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('stack')
                    ->label('Teknologi')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->limit(30),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'planning',
                        'info' => 'development',
                        'primary' => 'testing',
                        'success' => 'completed',
                        'secondary' => 'maintenance',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'planning',
                        'heroicon-o-code-bracket' => 'development',
                        'heroicon-o-bug-ant' => 'testing',
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-wrench-screwdriver' => 'maintenance',
                    ]),
                
                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->onIcon('heroicon-s-star')
                    ->offIcon('heroicon-o-star'),
                
                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('link')
                    ->label('Link')
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ? 'View Project' : 'No Link')
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'planning' => 'Planning',
                        'development' => 'Development',
                        'testing' => 'Testing',
                        'completed' => 'Completed',
                        'maintenance' => 'Maintenance',
                    ])
                    ->multiple(),
                
                TernaryFilter::make('is_featured')
                    ->label('Featured Projects')
                    ->placeholder('Semua Projects')
                    ->trueLabel('Featured Only')
                    ->falseLabel('Non-Featured Only'),
                
                Tables\Filters\Filter::make('tanggal_mulai')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('visit')
                        ->label('Visit Site')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('success')
                        ->url(fn ($record) => $record->link)
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => !empty($record->link)),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Mark as Featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_featured' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'completed']);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}
