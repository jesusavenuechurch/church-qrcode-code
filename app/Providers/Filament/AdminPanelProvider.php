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
use Filament\View\PanelsRenderHook; // Needed for CSS injection
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login() // Enables the login page

            // 🎨 BRANDING: Navy & Orange
            ->brandName('VENTIQ')
            ->brandLogo(asset('images/1.png')) // Ensure this image exists in public/images
            ->brandLogoHeight('5.5rem')
            ->favicon(asset('favicon.ico'))
            
            // 🎨 COLORS: Replacing Amber with Ventiq Identity
            ->colors([
                'primary' => '#1D4069', // Navy Blue
                'warning' => '#F07F22', // Orange (Use this for "Pending" statuses)
                'gray' => Color::Slate, // Slate works best with Navy
            ])
            ->font('Inter') // Modern, clean font

            // 🎨 CUSTOM LOGIN STYLING (CSS Injection)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render(<<<HTML
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

                        /* Primary Button (Sign In) */
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
                    </style>
                HTML)
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
           // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
              //  Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class, // Commented out to clean up dashboard
              //  \App\Filament\Widgets\StatsOverview::class,
             // \App\Filament\Widgets\ActionLauncher::class,
                \App\Filament\Widgets\TicketStatsWidget::class,
                \App\Filament\Widgets\PendingApprovalsWidget::class,
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