<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration() 
            ->emailVerification()

            // 🎨 BRANDING
            ->brandName('VENTIQ')
            ->brandLogo(asset('images/1.png'))
            ->brandLogoHeight('5.5rem')
            ->favicon(asset('favicon.ico'))
            ->assets([
            \Filament\Support\Assets\Css::make('ventiq-custom', asset('css/ventiq-admin.css')),
            ])
            ->maxContentWidth('full')
            
            // 🎨 COLORS
            ->colors([
                'primary' => '#1D4069',
                'warning' => '#F07F22',
                'gray' => Color::Slate,
            ])
            ->font('Inter')

            // 🎨 CUSTOM STYLING + DRIVER.JS
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render(<<<'HTML'
                    <!-- Driver.js CSS -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
                    
                    <style>
                        /* Login Page Background */
                        .fi-body.fi-panel-admin.fi-page-login {
                            background-color: #F8FAFC !important;
                            background-image: 
                                radial-gradient(circle at 10% 10%, rgba(29, 64, 105, 0.05) 0%, transparent 20%),
                                radial-gradient(circle at 90% 90%, rgba(240, 127, 34, 0.05) 0%, transparent 20%);
                        }
                        
                        /* Login Card Styling */
                        .fi-page-login .fi-simple-main-ctn {
                            background: rgba(255, 255, 255, 0.8);
                            backdrop-filter: blur(12px);
                            border: 1px solid rgba(255, 255, 255, 0.5);
                            box-shadow: 
                                0 4px 6px -1px rgba(0, 0, 0, 0.05), 
                                0 20px 25px -5px rgba(29, 64, 105, 0.1);
                            border-radius: 1.5rem;
                        }

                        /* Primary Button */
                        .fi-page-login .fi-btn-primary {
                            background-color: #1D4069 !important;
                            border-radius: 0.75rem;
                            font-weight: 700;
                            letter-spacing: 0.05em;
                            transition: all 0.3s ease;
                        }
                        .fi-page-login .fi-btn-primary:hover {
                            background-color: #153150 !important;
                            transform: translateY(-1px);
                            box-shadow: 0 4px 12px rgba(29, 64, 105, 0.2);
                        }

                        /* ========== DRIVER.JS CUSTOM STYLING ========== */
                        .driver-popover {
                            background: linear-gradient(135deg, #1D4069 0%, #2A5298 100%) !important;
                            border: 2px solid rgba(240, 127, 34, 0.3) !important;
                            border-radius: 1.5rem !important;
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
                            max-width: 400px !important;
                        }
                        
                        .driver-popover-title {
                            color: white !important;
                            font-size: 1.125rem !important;
                            font-weight: 900 !important;
                            text-transform: uppercase !important;
                            letter-spacing: 0.05em !important;
                            margin-bottom: 0.5rem !important;
                        }
                        
                        .driver-popover-description {
                            color: rgba(255, 255, 255, 0.85) !important;
                            font-size: 0.875rem !important;
                            line-height: 1.6 !important;
                        }
                        
                        .driver-popover-footer button {
                            background: #F07F22 !important;
                            color: white !important;
                            border: none !important;
                            border-radius: 0.75rem !important;
                            padding: 0.625rem 1.5rem !important;
                            font-weight: 700 !important;
                            text-transform: uppercase !important;
                            font-size: 0.75rem !important;
                            letter-spacing: 0.1em !important;
                            transition: all 0.2s !important;
                        }
                        
                        .driver-popover-footer button:hover {
                            background: #d96a1a !important;
                            transform: translateY(-2px) !important;
                            box-shadow: 0 10px 20px rgba(240, 127, 34, 0.3) !important;
                        }
                        
                        .driver-popover-prev-btn {
                            background: rgba(255, 255, 255, 0.1) !important;
                        }
                        
                        .driver-popover-prev-btn:hover {
                            background: rgba(255, 255, 255, 0.2) !important;
                            transform: translateY(-2px) !important;
                        }
                        
                        .driver-popover-arrow-side-top { border-top-color: #1D4069 !important; }
                        .driver-popover-arrow-side-bottom { border-bottom-color: #1D4069 !important; }
                        .driver-popover-arrow-side-left { border-left-color: #1D4069 !important; }
                        .driver-popover-arrow-side-right { border-right-color: #1D4069 !important; }

                        /* Pulse animation for highlighted elements */
                        @keyframes driver-pulse {
                            0%, 100% { box-shadow: 0 0 0 0 rgba(240, 127, 34, 0.7); }
                            50% { box-shadow: 0 0 0 20px rgba(240, 127, 34, 0); }
                        }
                        
                        .driver-active-element {
                            animation: driver-pulse 2s infinite !important;
                        }

                        /* Progress indicator styling */
                        .driver-popover-progress-text {
                            color: rgba(255, 255, 255, 0.6) !important;
                            font-size: 0.75rem !important;
                            font-weight: 600 !important;
                        }
                    </style>
                HTML)
            )

            // 🎯 DRIVER.JS SCRIPT (BODY END)
            ->renderHook(
                 PanelsRenderHook::BODY_END,
    fn (): string => view('filament.driver-tour')->render()
            )

            // ✅ SIDEBAR & UX
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // DISCOVERY
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Widgets\TicketStatsWidget::class,
                \App\Filament\Widgets\MobileAppDownload::class,
                \App\Filament\Widgets\WelcomeWidget::class,
                \App\Filament\Widgets\AgentWelcomeWidget::class,
            ])

            // MIDDLEWARE
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}