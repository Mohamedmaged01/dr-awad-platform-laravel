<?php

// Shared, presentational data ported from the Next.js prototype
// (src/lib/permissions.ts, src/components/layout/*). Arabic-first, RTL.

return [

    'contact' => [
        'phone_display' => '01005078266',
        'phone_tel' => '01005078266',
        'whatsapp' => '201005078266',
        'email' => 'info@dr-awad.com',
        'address' => "فرع بني مزار: شارع موقف صندفا - بجوار مسجد سيدنا حمزة\nفرع الشيخ زايد - القاهرة",
        'hours_short' => 'يومياً: 9 ص - 10 م',
        'youtube' => 'https://www.youtube.com/@DrMohamedAwad',
    ],

    // Main public navigation (Header.tsx). `name` is a translation key (see lang/*.json).
    'navigation' => [
        ['name' => 'home', 'href' => '/'],
        ['name' => 'about', 'href' => '/about'],
        ['name' => 'services', 'href' => '/services'],
        ['name' => 'videos', 'href' => '/videos'],
        ['name' => 'blog', 'href' => '/blog'],
        ['name' => 'contact', 'href' => '/contact'],
    ],

    // Footer quick links (Footer.tsx). `name` is a translation key.
    'footer_links' => [
        ['name' => 'home', 'href' => '/'],
        ['name' => 'about', 'href' => '/about'],
        ['name' => 'services', 'href' => '/services'],
        ['name' => 'videos', 'href' => '/videos'],
        ['name' => 'booking', 'href' => '/booking'],
        ['name' => 'blog', 'href' => '/blog'],
        ['name' => 'contact', 'href' => '/contact'],
    ],

    // Footer services. `name` is a translation key.
    'footer_services' => [
        ['name' => 'obgyn', 'href' => '/services/obstetrics-gynecology'],
        ['name' => 'ivfShort', 'href' => '/services/ivf-icsi'],
        ['name' => 'laparoscopy', 'href' => '/services/laparoscopy'],
        ['name' => 'pregnancyCare', 'href' => '/services/pregnancy-care'],
        ['name' => 'infertility', 'href' => '/services/infertility'],
    ],

    // Admin sidebar / RBAC matrix (permissions.ts DASHBOARD_MENU_ITEMS)
    // icon = lucide (blade-lucide-icons) kebab-case name.
    // `name` is a translation key (see lang/*.json).
    'dashboard_menu' => [
        ['name' => 'dashboard', 'href' => '/admin', 'icon' => 'layout-dashboard', 'roles' => ['admin', 'doctor', 'nurse', 'receptionist', 'lab_technician']],
        ['name' => 'patients', 'href' => '/admin/patients', 'icon' => 'users', 'roles' => ['admin', 'doctor', 'nurse', 'receptionist']],
        ['name' => 'appointments', 'href' => '/admin/appointments', 'icon' => 'calendar', 'roles' => ['admin', 'doctor', 'nurse', 'receptionist']],
        ['name' => 'surgeries', 'href' => '/admin/surgeries', 'icon' => 'stethoscope', 'roles' => ['admin', 'doctor']],
        ['name' => 'ivfCenter', 'href' => '/admin/ivf', 'icon' => 'microscope', 'roles' => ['admin', 'doctor', 'lab_technician']],
        ['name' => 'content_admin', 'href' => '/admin/content', 'icon' => 'file-text', 'roles' => ['admin', 'doctor']],
        ['name' => 'messages_admin', 'href' => '/admin/messages', 'icon' => 'message-square', 'roles' => ['admin', 'receptionist']],
        ['name' => 'reviews_admin', 'href' => '/admin/reviews', 'icon' => 'star', 'roles' => ['admin']],
        ['name' => 'payments', 'href' => '/admin/payments', 'icon' => 'credit-card', 'roles' => ['admin', 'receptionist']],
        ['name' => 'reports', 'href' => '/admin/reports', 'icon' => 'bar-chart-3', 'roles' => ['admin', 'doctor']],
        ['name' => 'branches', 'href' => '/admin/branches', 'icon' => 'building', 'roles' => ['admin']],
        ['name' => 'staff', 'href' => '/admin/staff', 'icon' => 'user-cog', 'roles' => ['admin']],
        ['name' => 'settings', 'href' => '/admin/settings', 'icon' => 'settings', 'roles' => ['admin']],
    ],

    // getRoleLabel() — values are translation keys.
    'role_labels' => [
        'admin' => 'roleAdmin',
        'doctor' => 'roleDoctor',
        'nurse' => 'roleNurse',
        'receptionist' => 'roleReceptionist',
        'lab_technician' => 'roleLabTechnician',
        'patient' => 'rolePatient',
    ],

    // Roles shown in the switcher / permissions matrix (excludes 'patient')
    'staff_roles' => ['admin', 'doctor', 'nurse', 'receptionist', 'lab_technician'],
];
