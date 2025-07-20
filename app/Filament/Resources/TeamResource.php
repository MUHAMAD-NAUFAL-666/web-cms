<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\HtmlString;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Tim';
    
    protected static ?string $modelLabel = 'Anggota Tim';
    
    protected static ?string $pluralModelLabel = 'Tim';

    protected static ?string $navigationGroup = 'Manajemen tim Cms(coding)';

    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->description('Informasi dasar anggota tim')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap'),
                                
                                TextInput::make('posisi')
                                    ->label('Posisi/Jabatan')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Prompt Developer ai'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('nama@example.com'),
                                
                                TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('+62 812-3456-7890'),
                            ]),
                        
                        FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('team-images')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                            ])
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        
                        Textarea::make('bio')
                            ->label('Biografi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Ceritakan tentang diri Anda...')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Informasi Profesional')
                    ->description('Detail pengalaman dan keahlian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('spesialisasi')
                                    ->label('Spesialisasi')
                                    ->maxLength(255)
                                    ->placeholder('Contoh: React, Laravel, UI/UX'),
                                
                                TextInput::make('pengalaman_tahun')
                                    ->label('Pengalaman (Tahun)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(50),
                            ]),
                        
                        DatePicker::make('tanggal_bergabung')
                            ->label('Tanggal Bergabung')
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'cuti' => 'Cuti',
                                'tidak_aktif' => 'Tidak Aktif',
                            ])
                            ->default('aktif')
                            ->required(),
                    ]),
                
                Section::make('Media Sosial')
                    ->description('Akun media sosial anggota tim')
                    ->schema([
                        Repeater::make('sosial_media')
                            ->label('Media Sosial')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('platform')
                                            ->label('Platform')
                                            ->options([
                                                'linkedin' => 'LinkedIn',
                                                'github' => 'GitHub',
                                                'twitter' => 'Twitter',
                                                'instagram' => 'Instagram',
                                                'facebook' => 'Facebook',
                                                'website' => 'Website Personal',
                                                'behance' => 'Behance',
                                                'dribbble' => 'Dribbble',
                                            ])
                                            ->required(),
                                        
                                        TextInput::make('url')
                                            ->label('URL')
                                            ->url()
                                            ->required()
                                            ->placeholder('https://...'),
                                    ]),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                            ->addActionLabel('Tambah Media Sosial')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Pengaturan')
                    ->description('Pengaturan tampilan dan status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->helperText('Anggota tim yang aktif akan ditampilkan di website')
                                    ->default(true),
                                
                                Toggle::make('is_featured')
                                    ->label('Unggulan')
                                    ->helperText('Anggota tim unggulan akan ditampilkan di halaman utama')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder-avatar.png')),
                
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),
                
                TextColumn::make('posisi')
                    ->label('Posisi')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->icon('heroicon-m-envelope')
                    ->url(fn ($record) => $record->email ? 'mailto:' . $record->email : null)
                    ->openUrlInNewTab(false)
                    ->color('primary')
                    ->tooltip('Klik untuk mengirim email'),
                
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon berhasil disalin!')
                    ->icon('heroicon-m-phone')
                    ->url(fn ($record) => $record->phone ? 'tel:' . $record->phone : null)
                    ->openUrlInNewTab(false)
                    ->color('success')
                    ->tooltip('Klik untuk menelepon')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                // Di bagian table columns, ganti kolom sosial_media dengan ini:

TextColumn::make('sosial_media')
    ->label('Media Sosial')
    ->formatStateUsing(function ($state, $record) {
        // Ambil data langsung dari record, bukan dari state
        $sosialMedia = $record->sosial_media;
        
        // Jika tidak ada data
        if (!$sosialMedia || empty($sosialMedia)) {
            return new HtmlString('<span class="text-gray-400">-</span>');
        }
        
        // Pastikan adalah array
        if (!is_array($sosialMedia)) {
            return new HtmlString('<span class="text-red-400">Bukan array</span>');
        }
        
        $icons = [
            'linkedin' => '🔗',
            'github' => '💻',
            'twitter' => '🐦',
            'instagram' => '📷',
            'facebook' => '👥',
            'website' => '🌐',
            'behance' => '🎨',
            'dribbble' => '🏀',
        ];
        
        $links = [];
        foreach ($sosialMedia as $social) {
            if (is_array($social) && isset($social['platform']) && isset($social['url']) && !empty($social['url'])) {
                $icon = $icons[$social['platform']] ?? '🔗';
                $platform = ucfirst($social['platform']);
                $url = htmlspecialchars($social['url']);
                
                $links[] = "<a href='{$url}' target='_blank' class='inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors' title='Buka {$platform}'>
                    <span>{$icon}</span>
                    <span>{$platform}</span>
                </a>";
            }
        }
        
        if (empty($links)) {
            return new HtmlString('<span class="text-gray-400">Tidak ada link valid</span>');
        }
        
        return new HtmlString('<div class="flex flex-wrap gap-1">' . implode(' ', $links) . '</div>');
    })
    ->html(),



                
                TextColumn::make('pengalaman_tahun')
                    ->label('Pengalaman')
                    ->suffix(' tahun')
                    ->sortable()
                    ->alignCenter(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'cuti',
                        'danger' => 'tidak_aktif',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'aktif',
                        'heroicon-o-clock' => 'cuti',
                        'heroicon-o-x-circle' => 'tidak_aktif',
                    ]),
                
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('gray'),
                
                ToggleColumn::make('is_featured')
                    ->label('Unggulan')
                    ->onColor('warning')
                    ->offColor('gray'),
                
                TextColumn::make('spesialisasi')
                    ->label('Spesialisasi')
                    ->searchable()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('tanggal_bergabung')
                    ->label('Bergabung')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'cuti' => 'Cuti',
                        'tidak_aktif' => 'Tidak Aktif',
                    ]),
                
                TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->placeholder('Semua anggota')
                    ->trueLabel('Hanya yang aktif')
                    ->falseLabel('Hanya yang tidak aktif'),
                
                TernaryFilter::make('is_featured')
                    ->label('Unggulan')
                    ->placeholder('Semua anggota')
                    ->trueLabel('Hanya yang unggulan')
                    ->falseLabel('Hanya yang tidak unggulan'),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
