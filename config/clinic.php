<?php

// Shared, presentational data ported from the Next.js prototype
// (src/lib/permissions.ts, src/components/layout/*). Arabic-first, RTL.

return [

    'contact' => [
        'phone_display' => '+20 123 456 7890',
        'phone_tel' => '+201234567890',
        'whatsapp' => '201234567890',
        'email' => 'info@dr-awad.com',
        'address' => 'شارع التحرير، الدقي، الجيزة، مصر',
        'hours_short' => 'السبت - الخميس: 9 ص - 9 م',
        'youtube' => 'https://www.youtube.com/@DrMohamedAwad',
    ],

    // Main public navigation (Header.tsx)
    'navigation' => [
        ['name' => 'الرئيسية', 'href' => '/'],
        ['name' => 'عن الدكتور', 'href' => '/about'],
        ['name' => 'الخدمات', 'href' => '/services'],
        ['name' => 'فيديوهات', 'href' => '/videos'],
        ['name' => 'المدونة', 'href' => '/blog'],
        ['name' => 'تواصل معنا', 'href' => '/contact'],
    ],

    // Footer quick links (Footer.tsx)
    'footer_links' => [
        ['name' => 'الرئيسية', 'href' => '/'],
        ['name' => 'عن الدكتور', 'href' => '/about'],
        ['name' => 'الخدمات', 'href' => '/services'],
        ['name' => 'فيديوهات', 'href' => '/videos'],
        ['name' => 'حجز موعد', 'href' => '/booking'],
        ['name' => 'المدونة', 'href' => '/blog'],
        ['name' => 'تواصل معنا', 'href' => '/contact'],
    ],

    'footer_services' => [
        ['name' => 'النساء والتوليد', 'href' => '/services/obstetrics-gynecology'],
        ['name' => 'الحقن المجهري', 'href' => '/services/ivf-icsi'],
        ['name' => 'جراحات المناظير', 'href' => '/services/laparoscopy'],
        ['name' => 'متابعة الحمل', 'href' => '/services/pregnancy-care'],
        ['name' => 'علاج العقم', 'href' => '/services/infertility'],
    ],

    // Admin sidebar / RBAC matrix (permissions.ts DASHBOARD_MENU_ITEMS)
    // icon = lucide (blade-lucide-icons) kebab-case name.
    'dashboard_menu' => [
        ['name' => 'لوحة التحكم', 'href' => '/admin', 'icon' => 'layout-dashboard', 'roles' => ['admin', 'doctor', 'nurse', 'receptionist', 'lab_technician']],
        ['name' => 'المريضات', 'href' => '/admin/patients', 'icon' => 'users', 'roles' => ['admin', 'doctor', 'nurse', 'receptionist']],
        ['name' => 'المواعيد', 'href' => '/admin/appointments', 'icon' => 'calendar', 'roles' => ['admin', 'doctor', 'nurse', 'receptionist']],
        ['name' => 'العمليات', 'href' => '/admin/surgeries', 'icon' => 'stethoscope', 'roles' => ['admin', 'doctor']],
        ['name' => 'مركز الحقن المجهري', 'href' => '/admin/ivf', 'icon' => 'microscope', 'roles' => ['admin', 'doctor', 'lab_technician']],
        ['name' => 'المحتوى', 'href' => '/admin/content', 'icon' => 'file-text', 'roles' => ['admin', 'doctor']],
        ['name' => 'الرسائل', 'href' => '/admin/messages', 'icon' => 'message-square', 'roles' => ['admin', 'receptionist']],
        ['name' => 'التقييمات', 'href' => '/admin/reviews', 'icon' => 'star', 'roles' => ['admin']],
        ['name' => 'المدفوعات', 'href' => '/admin/payments', 'icon' => 'credit-card', 'roles' => ['admin', 'receptionist']],
        ['name' => 'التقارير', 'href' => '/admin/reports', 'icon' => 'bar-chart-3', 'roles' => ['admin', 'doctor']],
        ['name' => 'الفروع', 'href' => '/admin/branches', 'icon' => 'building', 'roles' => ['admin']],
        ['name' => 'الفريق الطبي', 'href' => '/admin/staff', 'icon' => 'user-cog', 'roles' => ['admin']],
        ['name' => 'الإعدادات', 'href' => '/admin/settings', 'icon' => 'settings', 'roles' => ['admin']],
    ],

    // getRoleLabel()
    'role_labels' => [
        'admin' => 'مدير النظام',
        'doctor' => 'طبيب استشاري',
        'nurse' => 'طاقم تمريض',
        'receptionist' => 'موظف استقبال',
        'lab_technician' => 'فني معمل',
        'patient' => 'مريضة',
    ],

    // Roles shown in the switcher / permissions matrix (excludes 'patient')
    'staff_roles' => ['admin', 'doctor', 'nurse', 'receptionist', 'lab_technician'],
];
