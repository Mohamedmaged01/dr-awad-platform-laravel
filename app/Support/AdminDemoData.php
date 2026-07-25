<?php

namespace App\Support;

/**
 * Static demo data for the admin pages the prototype rendered with local mock
 * arrays (no backing model). Arabic-first, matching the prototype's admin UI.
 */
class AdminDemoData
{
    public static function surgeryStats(): array
    {
        return [
            ['label' => 'عمليات هذا الشهر', 'value' => '38', 'icon' => 'activity', 'color' => 'bg-blue-500'],
            ['label' => 'عمليات مجدولة', 'value' => '12', 'icon' => 'calendar', 'color' => 'bg-yellow-500'],
            ['label' => 'مكتملة', 'value' => '24', 'icon' => 'check-circle', 'color' => 'bg-green-500'],
            ['label' => 'نسبة النجاح', 'value' => '98%', 'icon' => 'trending-up', 'color' => 'bg-purple-500'],
        ];
    }

    public static function surgeries(): array
    {
        return [
            ['id' => 1, 'patient' => 'سارة أحمد', 'file' => 'P2024001', 'operation' => 'منظار رحم تشخيصي', 'type' => 'منظار', 'date' => '2024-01-22', 'time' => '10:00', 'doctor' => 'د. محمد عوض', 'cost' => 12000, 'status' => 'scheduled'],
            ['id' => 2, 'patient' => 'منى خالد', 'file' => 'P2024014', 'operation' => 'استئصال كيس مبيض', 'type' => 'منظار بطن', 'date' => '2024-01-18', 'time' => '12:30', 'doctor' => 'د. محمد عوض', 'cost' => 18000, 'status' => 'completed'],
            ['id' => 3, 'patient' => 'نورا محمد', 'file' => 'P2024022', 'operation' => 'إزالة ورم ليفي', 'type' => 'منظار', 'date' => '2024-01-25', 'time' => '09:30', 'doctor' => 'د. محمد عوض', 'cost' => 22000, 'status' => 'pending'],
            ['id' => 4, 'patient' => 'هالة عبدالله', 'file' => 'P2024031', 'operation' => 'ولادة قيصرية', 'type' => 'جراحة', 'date' => '2024-01-15', 'time' => '08:00', 'doctor' => 'د. محمد عوض', 'cost' => 25000, 'status' => 'completed'],
        ];
    }

    public static function paymentStats(): array
    {
        return [
            ['label' => 'إجمالي الإيرادات', 'value' => '485,000 ج.م', 'icon' => 'dollar-sign', 'color' => 'bg-green-500'],
            ['label' => 'مدفوعات هذا الشهر', 'value' => '112,000 ج.م', 'icon' => 'wallet', 'color' => 'bg-blue-500'],
            ['label' => 'نمو شهري', 'value' => '+18%', 'icon' => 'trending-up', 'color' => 'bg-purple-500'],
            ['label' => 'فواتير معلقة', 'value' => '7', 'icon' => 'credit-card', 'color' => 'bg-yellow-500'],
        ];
    }

    public static function invoices(): array
    {
        return [
            ['id' => 'INV-2024-001', 'patient' => 'سارة أحمد', 'service' => 'استشارة حقن مجهري', 'amount' => 700, 'method' => 'فيزا', 'status' => 'paid', 'date' => '2024-01-20'],
            ['id' => 'INV-2024-002', 'patient' => 'منى خالد', 'service' => 'متابعة حمل + سونار 4D', 'amount' => 600, 'method' => 'نقدي', 'status' => 'paid', 'date' => '2024-01-19'],
            ['id' => 'INV-2024-003', 'patient' => 'نورا محمد', 'service' => 'كشف طبي عام', 'amount' => 500, 'method' => 'فودافون كاش', 'status' => 'pending', 'date' => '2024-01-18'],
            ['id' => 'INV-2024-004', 'patient' => 'ريم سعيد', 'service' => 'استشارة جراحية', 'amount' => 600, 'method' => 'فيزا', 'status' => 'paid', 'date' => '2024-01-17'],
        ];
    }

    public static function reportKpis(): array
    {
        return [
            ['label' => 'إجمالي المريضات', 'value' => '2,450', 'change' => '+12%', 'up' => true, 'icon' => 'users', 'color' => 'bg-blue-500'],
            ['label' => 'مواعيد الشهر', 'value' => '386', 'change' => '+8%', 'up' => true, 'icon' => 'calendar', 'color' => 'bg-green-500'],
            ['label' => 'عمليات ناجحة', 'value' => '38', 'change' => '+5%', 'up' => true, 'icon' => 'activity', 'color' => 'bg-purple-500'],
            ['label' => 'إجمالي الإيرادات', 'value' => '485 ألف', 'change' => '+18%', 'up' => true, 'icon' => 'dollar-sign', 'color' => 'bg-yellow-500'],
        ];
    }

    /** 12 monthly values for the hand-rolled bar chart. */
    public static function monthlyGrowth(): array
    {
        return [65, 80, 45, 90, 70, 110, 95, 120, 100, 130, 115, 140];
    }

    /** Service distribution for the donut chart (label, percent, color class). */
    public static function serviceDistribution(): array
    {
        return [
            ['label' => 'النساء والتوليد', 'percent' => 40, 'color' => 'bg-medical-blue'],
            ['label' => 'الحقن المجهري', 'percent' => 30, 'color' => 'bg-light-gold'],
            ['label' => 'جراحات المناظير', 'percent' => 18, 'color' => 'bg-pink-500'],
            ['label' => 'أمراض النساء', 'percent' => 12, 'color' => 'bg-emerald-500'],
        ];
    }

    public static function reviews(): array
    {
        return [
            ['patient' => 'سارة أحمد', 'rating' => 5, 'comment' => 'دكتور ممتاز ومتابعة رائعة، أنصح به بشدة.', 'service' => 'الحقن المجهري', 'status' => 'approved'],
            ['patient' => 'منى خالد', 'rating' => 5, 'comment' => 'تعامل راقي ونتائج مبهرة، شكراً لكم.', 'service' => 'متابعة الحمل', 'status' => 'approved'],
            ['patient' => 'نورا محمد', 'rating' => 4, 'comment' => 'تجربة جيدة جداً والتعافي كان سريعاً.', 'service' => 'جراحات المناظير', 'status' => 'pending'],
            ['patient' => 'ريم سعيد', 'rating' => 5, 'comment' => 'أفضل عيادة نساء وتوليد تعاملت معها.', 'service' => 'النساء والتوليد', 'status' => 'approved'],
        ];
    }

    public static function staff(): array
    {
        return [
            ['name' => 'د. محمد عوض', 'role' => 'طبيب استشاري', 'email' => 'dr.awad@dr-awad.com', 'phone' => '01005078266', 'status' => 'active'],
            ['name' => 'أ. فاطمة سيد', 'role' => 'طاقم تمريض', 'email' => 'fatma@dr-awad.com', 'phone' => '01012345671', 'status' => 'active'],
            ['name' => 'أ. أحمد علي', 'role' => 'موظف استقبال', 'email' => 'ahmed@dr-awad.com', 'phone' => '01012345672', 'status' => 'active'],
            ['name' => 'أ. سمر حسن', 'role' => 'فني معمل', 'email' => 'samar@dr-awad.com', 'phone' => '01012345673', 'status' => 'vacation'],
        ];
    }

    public static function branches(): array
    {
        return [
            ['name' => 'فرع بني مزار', 'address' => 'شارع موقف صندفا - بجوار مسجد سيدنا حمزة', 'phone' => '01005078266', 'hours' => 'يومياً 9 ص - 10 م'],
            ['name' => 'فرع الشيخ زايد', 'address' => 'الشيخ زايد - القاهرة', 'phone' => '01005078266', 'hours' => 'يومياً 9 ص - 10 م'],
        ];
    }

    public static function content(): array
    {
        return [
            ['page' => 'home', 'section' => 'hero', 'key' => 'heroTitle', 'type' => 'text', 'preview' => 'د. محمد عوض'],
            ['page' => 'home', 'section' => 'hero', 'key' => 'heroSubtitle', 'type' => 'text', 'preview' => 'استشاري النساء والتوليد والحقن المجهري'],
            ['page' => 'about', 'section' => 'bio', 'key' => 'aboutBio', 'type' => 'text', 'preview' => 'خبرة تزيد عن 20 عاماً...'],
            ['page' => 'global', 'section' => 'contact', 'key' => 'phone', 'type' => 'text', 'preview' => '01005078266'],
        ];
    }

    public static function messages(): array
    {
        return [
            ['name' => 'سارة أحمد', 'email' => 'sara@email.com', 'phone' => '01012345678', 'subject' => 'استفسار عن الحقن المجهري', 'message' => 'السلام عليكم، أريد الاستفسار عن مواعيد وتكلفة الحقن المجهري. شكراً لكم.', 'time' => 'منذ 10 دقائق', 'unread' => true],
            ['name' => 'منى خالد', 'email' => 'mona@email.com', 'phone' => '01087654321', 'subject' => 'حجز موعد متابعة حمل', 'message' => 'أريد حجز موعد لمتابعة الحمل في الأسبوع القادم إن أمكن.', 'time' => 'منذ ساعة', 'unread' => true],
            ['name' => 'نورا محمد', 'email' => 'nora@email.com', 'phone' => '01011223344', 'subject' => 'شكر وتقدير', 'message' => 'شكراً جزيلاً على التعامل الرائع والمتابعة المستمرة.', 'time' => 'منذ 3 ساعات', 'unread' => false],
        ];
    }
}
