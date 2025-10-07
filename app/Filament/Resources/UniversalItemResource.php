<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Filament\Resources\UniversalItemResource\Pages\ListUniversalItems;
use App\Filament\Resources\UniversalItemResource\Pages\CreateUniversalItem;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use App\Models\Media;

class UniversalItemResource extends Resource
{
    // Use a generic model or create a base model
    protected static ?string $model = Product::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    
    protected static ?string $navigationLabel = 'Add Items';
    
    protected static ?string $pluralModelLabel = 'Items';

    protected static array $tableConfigs = [
        'products' => [
            'model' => Product::class,
            'label' => 'Product',
            'fields' => [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|file|image|max:2048'
            ]
        ],
        'articles' => [
            'model' => Article::class,
            'label' => 'Article',
            'fields' => [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'author' => 'required|string|max:255',
                'published_at' => 'nullable|date',
                'featured_image' => 'nullable|file|image|max:2048'
            ]
        ],
        'users' => [
            'model' => User::class,
            'label' => 'User',
            'fields' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|string',
                'avatar' => 'nullable|file|image|max:1024'
            ]
        ],
        'categories' => [
            'model' => Category::class,
            'label' => 'Category',
            'fields' => [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug',
                'description' => 'nullable|string',
                'icon' => 'nullable|file|image|max:512'
            ]
        ],
        'media' => [
            'model' => Media::class,
            'label' => 'Media File',
            'fields' => [
                'title' => 'required|string|max:255',
                'type' => 'required|string',
                'file_path' => 'required|file|max:10240',
                'alt_text' => 'nullable|string|max:255'
            ]
        ]
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Item Type Selection')
                    ->schema([
                        Select::make('table_type')
                            ->label('What type of item do you want to create?')
                            ->options(collect(self::$tableConfigs)->mapWithKeys(fn($config, $key) => [$key => $config['label']]))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('item_data', []);
                            })
                            ->columnSpanFull()
                    ]),

                Section::make('Item Details')
                    ->schema(fn (Get $get): array => self::getDynamicFields($get('table_type')))
                    ->visible(fn (Get $get): bool => filled($get('table_type')))
            ]);
    }

    protected static function getDynamicFields(?string $tableType): array
    {
        if (!$tableType || !isset(self::$tableConfigs[$tableType])) {
            return [];
        }

        $config = self::$tableConfigs[$tableType];
        $fields = [];

        switch ($tableType) {
            case 'products':
                $fields = [
                    TextInput::make('item_data.name')
                        ->label('Product Name')
                        ->required()
                        ->maxLength(255),
                    
                    TextInput::make('item_data.price')
                        ->label('Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0),
                    
                    Textarea::make('item_data.description')
                        ->label('Description')
                        ->rows(3),
                    
                    Select::make('item_data.category_id')
                        ->label('Category')
                        ->options(Category::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    
                    FileUpload::make('item_data.image')
                        ->label('Product Image')
                        ->image()
                        ->directory('products')
                        ->maxSize(2048)
                ];
                break;

            case 'articles':
                $fields = [
                    TextInput::make('item_data.title')
                        ->label('Article Title')
                        ->required()
                        ->maxLength(255),
                    
                    TextInput::make('item_data.author')
                        ->label('Author')
                        ->required()
                        ->maxLength(255),
                    
                    RichEditor::make('item_data.content')
                        ->label('Content')
                        ->required()
                        ->columnSpanFull(),
                    
                    DatePicker::make('item_data.published_at')
                        ->label('Published Date')
                        ->default(now()),
                    
                    FileUpload::make('item_data.featured_image')
                        ->label('Featured Image')
                        ->image()
                        ->directory('articles')
                        ->maxSize(2048)
                ];
                break;

            case 'users':
                $fields = [
                    TextInput::make('item_data.name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),
                    
                    TextInput::make('item_data.email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->unique(User::class, 'email'),
                    
                    TextInput::make('item_data.password')
                        ->label('Password')
                        ->password()
                        ->required()
                        ->minLength(8)
                        ->revealable(),
                    
                    Select::make('item_data.role')
                        ->label('Role')
                        ->options([
                            'admin' => 'Administrator',
                            'editor' => 'Editor',
                            'user' => 'User'
                        ])
                        ->required(),
                    
                    FileUpload::make('item_data.avatar')
                        ->label('Profile Picture')
                        ->image()
                        ->directory('avatars')
                        ->maxSize(1024)
                ];
                break;

            case 'categories':
                $fields = [
                    TextInput::make('item_data.name')
                        ->label('Category Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('item_data.slug', Str::slug($state))),
                    
                    TextInput::make('item_data.slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Category::class, 'slug'),
                    
                    Textarea::make('item_data.description')
                        ->label('Description')
                        ->rows(3),
                    
                    FileUpload::make('item_data.icon')
                        ->label('Category Icon')
                        ->image()
                        ->directory('categories')
                        ->maxSize(512)
                ];
                break;

            case 'media':
                $fields = [
                    TextInput::make('item_data.title')
                        ->label('Media Title')
                        ->required()
                        ->maxLength(255),
                    
                    Select::make('item_data.type')
                        ->label('Media Type')
                        ->options([
                            'image' => 'Image',
                            'video' => 'Video',
                            'document' => 'Document',
                            'audio' => 'Audio'
                        ])
                        ->required(),
                    
                    FileUpload::make('item_data.file_path')
                        ->label('Upload File')
                        ->required()
                        ->directory('media')
                        ->maxSize(10240), // 10MB
                    
                    TextInput::make('item_data.alt_text')
                        ->label('Alt Text (for accessibility)')
                        ->maxLength(255)
                ];
                break;
        }

        return $fields;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // You might want to customize these based on the item type
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getTableConfigs(): array
    {
        return self::$tableConfigs;
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
            'index' => CreateUniversalItem::route('/'),
            'create' => ListUniversalItems::route('/create'),
            'edit' => ListUniversalItems::route('/{record}/edit'),
        ];
    }
}