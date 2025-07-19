<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtikelResource\Pages;
use App\Models\Artikel;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArtikelResource extends Resource
{
    protected static ?string $model = Artikel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Konten';
    protected static ?string $label = 'Artikel';
    protected static ?string $pluralLabel = 'Daftar Artikel';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Artikel')
                    ->description('Masukkan detail artikel yang akan dipublikasikan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Hidden::make('user_id')->default(fn () => Auth::id()),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Artikel')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                        $context === 'create' ? $set('slug', Str::slug($state)) : null
                                    )
                                    ->columnSpan(2)
                                    ->placeholder('Masukkan judul artikel yang menarik'),

                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'publish' => 'Publish',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),

                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1)
                                    ->placeholder('url-artikel-anda'),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Section::make('Konten Artikel')
                    ->description('Tulis konten artikel Anda di sini')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        RichEditor::make('isi')
                            ->label('Isi Artikel')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->placeholder('Mulai menulis artikel Anda...'),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Section::make('Media & Metadata')
                    ->description('Upload gambar dan atur metadata artikel')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('gambar')
                                    ->label('Gambar Artikel')
                                    ->image()
                                    ->directory('artikel-images')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->columnSpan(1)
                                    ->helperText('Upload gambar dengan ukuran maksimal 2MB'),

                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(60)
                                            ->helperText('Optimal: 50-60 karakter'),

                                        Textarea::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(160)
                                            ->rows(3)
                                            ->helperText('Optimal: 150-160 karakter'),

                                        TextInput::make('reading_time')
                                            ->label('Waktu Baca (menit)')
                                            ->numeric()
                                            ->suffix('menit')
                                            ->placeholder('5'),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->size(80)
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder.jpg'))
                    ->extraImgAttributes(['loading' => 'lazy']),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'danger' => 'draft',
                        'success' => 'publish',
                        'warning' => 'archived',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-eye' => 'publish',
                        'heroicon-o-archive-box' => 'archived',
                    ]),

                TextColumn::make('user.name')
                    ->label('Penulis')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->iconColor('primary'),

                TextColumn::make('reading_time')
                    ->label('Waktu Baca')
                    ->suffix(' menit')
                    ->placeholder('—')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'publish' => 'Publish',
                        'archived' => 'Archived',
                    ])
                    ->multiple()
                    ->placeholder('Semua Status'),

                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Penulis'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('primary')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Terpilih')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'publish']);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Publish Artikel')
                        ->modalDescription('Apakah Anda yakin ingin mempublish artikel yang dipilih?'),
                    Tables\Actions\BulkAction::make('draft')
                        ->label('Jadikan Draft')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'draft']);
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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
            'index' => Pages\ListArtikels::route('/'),
            'create' => Pages\CreateArtikel::route('/create'),
            'edit' => Pages\EditArtikel::route('/{record}/edit'),
            'view' => Pages\ViewArtikel::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'draft')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'draft')->count() > 0 ? 'warning' : 'primary';
    }
}
