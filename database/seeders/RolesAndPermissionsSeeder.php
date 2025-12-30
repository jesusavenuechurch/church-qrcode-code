<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== Setting Up Roles & Permissions ===\n";

        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // ===== PERMISSIONS =====
        echo "Creating Permissions...\n";

        // Organization Management
        Permission::firstOrCreate(['name' => 'create_organization']);
        Permission::firstOrCreate(['name' => 'edit_organization']);
        Permission::firstOrCreate(['name' => 'view_organization']);
        Permission::firstOrCreate(['name' => 'delete_organization']);

        // Event Management
        Permission::firstOrCreate(['name' => 'create_event']);
        Permission::firstOrCreate(['name' => 'edit_event']);
        Permission::firstOrCreate(['name' => 'view_event']);
        Permission::firstOrCreate(['name' => 'delete_event']);
        Permission::firstOrCreate(['name' => 'publish_event']);

        // Event Tier Management
        Permission::firstOrCreate(['name' => 'create_event_tier']);
        Permission::firstOrCreate(['name' => 'edit_event_tier']);
        Permission::firstOrCreate(['name' => 'view_event_tier']);
        Permission::firstOrCreate(['name' => 'delete_event_tier']);

        // Client Management
        Permission::firstOrCreate(['name' => 'create_client']);
        Permission::firstOrCreate(['name' => 'edit_client']);
        Permission::firstOrCreate(['name' => 'view_client']);
        Permission::firstOrCreate(['name' => 'delete_client']);
        Permission::firstOrCreate(['name' => 'bulk_upload_clients']);

        // Ticket Management
        Permission::firstOrCreate(['name' => 'create_ticket']);
        Permission::firstOrCreate(['name' => 'edit_ticket']);
        Permission::firstOrCreate(['name' => 'view_ticket']);
        Permission::firstOrCreate(['name' => 'refund_ticket']);
        Permission::firstOrCreate(['name' => 'resend_ticket']);

        // Payment Management
        Permission::firstOrCreate(['name' => 'approve_payment']);
        Permission::firstOrCreate(['name' => 'reject_payment']);
        Permission::firstOrCreate(['name' => 'view_payments']);

        // Check-in / Scanning
        Permission::firstOrCreate(['name' => 'scan_qr']);
        Permission::firstOrCreate(['name' => 'view_checkins']);

        // Partner Management (Sponsors)
        Permission::firstOrCreate(['name' => 'create_partner']);
        Permission::firstOrCreate(['name' => 'edit_partner']);
        Permission::firstOrCreate(['name' => 'view_partner']);
        Permission::firstOrCreate(['name' => 'delete_partner']);
        Permission::firstOrCreate(['name' => 'email_partners']);

        // Reporting
        Permission::firstOrCreate(['name' => 'view_reports']);
        Permission::firstOrCreate(['name' => 'export_reports']);
        Permission::firstOrCreate(['name' => 'view_dashboard']);

        // Staff Management
        Permission::firstOrCreate(['name' => 'manage_staff']);
        Permission::firstOrCreate(['name' => 'manage_roles']);

        // Payment Method permissions
        Permission::firstOrCreate(['name' => 'view_payment_method']);
        Permission::firstOrCreate(['name' => 'create_payment_method']);
        Permission::firstOrCreate(['name' => 'edit_payment_method']);
        Permission::firstOrCreate(['name' => 'delete_payment_method']);

        echo "✅ " . Permission::count() . " permissions created\n\n";

        // ===== ROLES =====
        echo "Creating Roles...\n";

        // SUPER ADMIN - Full system access
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());
        echo "✅ Super Admin role (all permissions)\n";

        // ORG ADMIN - Can manage their organization
        $orgAdmin = Role::firstOrCreate(['name' => 'org_admin']);
        $orgAdmin->syncPermissions([
            'view_organization',
            'edit_organization',
            'create_event',
            'edit_event',
            'view_event',
            'delete_event',
            'publish_event',
            'create_event_tier',
            'edit_event_tier',
            'view_event_tier',
            'delete_event_tier',
            'create_client',
            'edit_client',
            'view_client',
            'delete_client',
            'bulk_upload_clients',
            'create_ticket',
            'edit_ticket',
            'view_ticket',
            'refund_ticket',
            'resend_ticket',
            'approve_payment',
            'reject_payment',
            'view_payments',
            'scan_qr',
            'view_checkins',
            'create_partner',
            'edit_partner',
            'view_partner',
            'delete_partner',
            'email_partners',
            'view_reports',
            'export_reports',
            'view_dashboard',
            'manage_staff',
            'view_payment_method',
            'create_payment_method',
            'edit_payment_method',
            'delete_payment_method',
        ]);
        echo "✅ Org Admin role\n";

        // STAFF - Can create clients, tickets, approve payments
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'create_client',
            'edit_client',
            'view_client',
            'create_ticket',
            'view_ticket',
            'resend_ticket',
            'approve_payment',
            'view_payments',
            'view_checkins',
            'view_reports',
            'view_dashboard',
            'view_payment_method',
        ]);
        echo "✅ Staff role\n";

        // SCANNER - Only scan QR codes
        $scanner = Role::firstOrCreate(['name' => 'scanner']);
        $scanner->syncPermissions([
            'scan_qr',
            'view_checkins',
        ]);
        echo "✅ Scanner role\n";

        // VIEWER - Read-only access
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'view_event',
            'view_client',
            'view_ticket',
            'view_checkins',
            'view_reports',
            'view_dashboard',
        ]);
        echo "✅ Viewer role\n\n";

        // ===== CREATE DEFAULT SUPER ADMIN USER =====
        echo "Creating Default Super Admin User...\n";

        // Check if super admin already exists
        $existingSuperAdmin = User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->first();

        if ($existingSuperAdmin) {
            echo "⚠️  Super Admin already exists: {$existingSuperAdmin->email}\n";
        } else {
            // Create super admin
            $adminUser = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@system.local',
                'password' => Hash::make('password'), // Change this in production!
                'organization_id' => null, // Super admin has no org
            ]);

            $adminUser->assignRole('super_admin');

            echo "✅ Super Admin created successfully!\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "📧 Email: admin@system.local\n";
            echo "🔑 Password: password\n";
            echo "⚠️  IMPORTANT: Change this password immediately!\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        }

        echo "\n✅ Roles & Permissions setup complete!\n";
    }
}
